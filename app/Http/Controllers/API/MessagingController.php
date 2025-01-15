<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Helper\Helper;
use App\Traits\apiresponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\blockusercheck;
use Namu\WireChat\Models\Participant;
use Namu\WireChat\Events\MessageCreated;
use Illuminate\Support\Facades\Validator;
use Namu\WireChat\Events\NotifyParticipant;

class MessagingController extends Controller
{
    use apiresponse;
    use blockusercheck;

    /**
     * Get Conversations
     * @return \Illuminate\Http\Response
     */
    public function getConversations()
    {
        $user = auth()->user();
        $conversations = $user->conversations()->with(['participants' => function ($query) {
            $query->where('participantable_id', '!=', auth()->id());
        }, 'lastMessage'])->get();
        return $this->success([
            'conversations' => $conversations,
        ], "Conversations fetched successfully", 200);
    }
    /**
     * Send Message
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'to_user_id' => 'required|exists:users,id',
            'message' => 'required_without:file|string',
            'file' => 'required_without:message|file|mimes:png,jpg,jpeg,webp|max:2048',
        ]);

        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 422); // Use 422 for validation errors
        }

        DB::beginTransaction();
        try {
            // Blocked User Check // User blocked check
            if($this->checkUserBlocked($request->to_user_id)){
                return $this->error([], "This user is blocked.", 403);
            }elseif($this->checkBlockedMe($request->to_user_id)){
                return $this->error([], "This user has blocked you.", 403);
            }

            $auth = auth()->user();
            $recipient = User::where('id', $request->to_user_id)
                ->where('id', '!=', $auth->id) // Prevent sending messages to self
                ->where('status', 'active') // Ensure user is active
                ->first();

            if (!$recipient) {
                return $this->error([], 'Recipient not found', 404);
            }
            $sendMessage = $request->message;
            if($request->hasFile('file') && $request->file('file')->isValid() && $request->message == null){ 
                $rand = Str::random(6);
                $sendMessage= Helper::fileUpload($request->file('file'), 'message', "User-" . $auth->username . "-" . $rand . "-" . time());
            }

            // Use the sendMessageTo method from the Chatable trait
            $message = $auth->sendMessageTo($recipient, $sendMessage);

            // Broadcast events after successful message creation
            broadcast(new MessageCreated($message));
            broadcast(new NotifyParticipant($message->conversation->participant($recipient), $message));

            DB::commit();

            return $this->success(['message' => $message], "Message sent successfully", 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 500);
        }
    }

    /**
     * Get users Conversation
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function getUserConversation(User $user)
    {
        $otherUser = User::findOrFail($user->id);
        $con = $otherUser->conversations()->with(['participants' => function ($query) {
            $query->where('participantable_id', auth()->id());
        }, 'messages'])->first();

        return $this->success([
            'conversations' => $con,
            'youblocked' => $this->checkUserBlocked($user->id),
            'blockedyou' => $this->checkBlockedMe($user->id),
        ], "Conversations fetched successfully", 200);
    }

}
