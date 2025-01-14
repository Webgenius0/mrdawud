<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Namu\WireChat\Events\MessageCreated;
use Namu\WireChat\Events\NotifyParticipant;
use Namu\WireChat\Models\Participant;

class MessagingController extends Controller
{
    use apiresponse;

    /**
     * Get Conversations
     * @return \Illuminate\Http\Response
     */
    public function getConversations()
    {
        $user = auth()->user();
        $conversations = $user->conversations()->with([ 'participants','lastMessage'])->get();
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
            'message' => 'required|string',
        ]);

        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 500);
        }

        DB::beginTransaction();
        try {
            $otherUser = User::where('id', $request->to_user_id)
            // ->where('role', 'instructor')// For Production
            ->where('id', '!=', auth()->id)
            ->where('status', 'active')
            ->first();

            if (!$otherUser) {
                return $this->error([], 'User not found', 404);
            }
            $auth = auth()->user();
            $conversation = $auth->conversations()->first();
            if (!$conversation) {
                $conversation = $auth->createConversationWith($otherUser);
            }
            $message = $auth->sendMessageTo($conversation, $request->message);

            // Retrieve or create a Participant instance
            $participant = Participant::firstOrCreate([
                'participantable_id' => $otherUser->id,
                'participantable_type' => get_class($otherUser),
                'conversation_id' => $conversation->id,
            ]);

            broadcast(new MessageCreated($message));
            broadcast(new NotifyParticipant($participant, $message));
            DB::commit();

            return $this->success([], "Message sent successfully", 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 500);
        }
        return $this->success([], "Message sent successfully", 200);
    }
}
