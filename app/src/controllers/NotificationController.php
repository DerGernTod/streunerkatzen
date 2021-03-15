<?php

namespace Streunerkatzen\Controllers;

use PageController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use Streunerkatzen\CatSearch\SearchAgent;

class NotificationController extends PageController {
    private static $allowed_actions = [
        'unsubcatsearch'
    ];

    public function unsubcatsearch(HTTPRequest $request) {
        $getVars = $request->getVars();
        if (isset($getVars["token"]) &&
            isset($getVars["email"])) {
            $matchingAgents = SearchAgent::get()->filter([
                "Token" => $getVars["token"],
                "Email" => $getVars["email"]
            ]);

            $templateData = [
                'MatchFound' => false
            ];

            if (count($matchingAgents) > 0) {
                $templateData['MatchFound'] = true;
                $agents = new ArrayList();
                foreach ($matchingAgents as $agent) {
                    $agentData = new ArrayData([
                        'Email' => $agent->Email,
                        'ReadableSearch' => $agent->getReadableSearch()
                    ]);
                    $agents->push($agentData);
                }
                $templateData['Agents'] = $agents;

                $matchingAgents->removeAll();
            }

            return $templateData;
        }

        return $this->httpError(404, 'Ungültige Anfrage: fehlende Parameter');
    }
}
