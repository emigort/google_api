<?php

namespace App\Http\Controllers;

use App\Clients\GoogleApi;
use App\Models\User;
use Carbon\Carbon;
use Google\Service\Exception;
use Google\Service\YouTube;
use Google\Service\YouTube\LiveBroadcast;
use Google\Service\YouTube\LiveBroadcastContentDetails;
use Google\Service\YouTube\LiveBroadcastSnippet;
use Google\Service\YouTube\LiveBroadcastStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class YoutubeController extends Controller
{
    private GoogleApi $googleApi;

    public function __construct()
    {
        $this->googleApi = new GoogleApi();
    }

    /**
     * @throws Exception
     */
    public function createBroadcast(Request $request): JsonResponse
    {
        $user = User::first();

        $client = $this->googleApi->refreshUserToken($user);

        $youtube = new YouTube($client);
        $broadcast = new LiveBroadcast();
        // The Broadcast Snippet
        $broadcast_snippet = new LiveBroadcastSnippet();
        $broadcast_snippet->setTitle('Emilio Testing Broadcast');
        $broadcast_snippet->setDescription('This is a test broadcast');
        $broadcast_snippet->setScheduledStartTime($this->getNext8pm()->format('Y-m-d\TH:i:s.000\Z'));
        $broadcast_snippet->setScheduledEndTime($this->getNext8pm()->addMinutes(30)->format('Y-m-d\TH:i:s.000\Z'));
        $broadcast->setSnippet($broadcast_snippet);

        // The Broadcast ContentDetails
        $broadcast_content_detail = new LiveBroadcastContentDetails();
        $broadcast_content_detail->setEnableClosedCaptions(true);
        $broadcast_content_detail->setEnableContentEncryption(true);
        $broadcast_content_detail->setEnableDvr(true);
        $broadcast_content_detail->setEnableEmbed(true);
        $broadcast_content_detail->setRecordFromStart(true);
        $broadcast_content_detail->setStartWithSlate(true);
        $broadcast->setContentDetails($broadcast_content_detail);

        // The Broadcast Status
        $broadcast_status = new LiveBroadcastStatus();
        $broadcast_status->setPrivacyStatus('public');
        $broadcast->setStatus($broadcast_status);

        $response = $youtube->liveBroadcasts->insert('snippet,contentDetails,status', $broadcast);

        return response()->json($response);
    }

    /**
     * @throws Exception
     */
    public function deleteBroadcast($broadcastId): JsonResponse
    {
        $user = User::first();
        $client = $this->googleApi->refreshUserToken($user);

        $youtube = new YouTube($client);
        $response = $youtube->liveBroadcasts->delete($broadcastId);
        return response()->json($response);
    }

    private function getNext8pm(): Carbon
    {
        $next8pm = Carbon::today('America/New_York')
            ->setTime(20,0);

        if(Carbon::now()->greaterThan($next8pm)){
            $next8pm->addDay();
        }
        return $next8pm->setTimezone('UTC');
    }
}
