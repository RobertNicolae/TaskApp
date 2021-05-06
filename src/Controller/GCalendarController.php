<?php

namespace App\Controller;

use App\Entity\Task;
use Google_Client;
use Google_Exception;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @Route("/g", name="g.")
 * Class GCalendarController
 * @package App\Controller
 */
class GCalendarController extends AbstractController
{

    /**
     * @Route("/calendar/{id}", name="calendar")
     * @param Request $request
     * @param Task $task
     * @return JsonResponse
     * @throws Google_Exception
     */
    public function index(Request $request, Task $task)
    {

        $client = new Google_Client();
        $client->setAuthConfig("client_secret.json");
        $client->addScope(Google_Service_Calendar::CALENDAR);
//        $token = $client->fetchAccessTokenWithAuthCode($request->request->get("code"));
        $token = $client->fetchAccessTokenWithRefreshToken($request->get("code"));

        $client->setAccessToken($token);
        $service = new Google_Service_Calendar($client);

        $format = 'Y-m-d\TH:i:sP'; //DATE_ATOM


        $taskDate = $task->getDeadlineDate();
        $taskCreated = $task->getCreated();
        $endDate = $taskDate->format($format);
        $startDate = $taskCreated->format($format);


        $calendarId = 'primary';

        $event = new Google_Service_Calendar_Event(array(
            'summary' => $task->getName(),
            'description' => $task->getDescription(),
            'start' => array(
                'dateTime' =>   $startDate,
                'timeZone' => 'America/Los_Angeles',
            ),
            'end' => array(
                    'dateTime' => $endDate,
                'timeZone' => 'America/Los_Angeles',
            ),
            'reminders' => array(
                'useDefault' => FALSE,
                'overrides' => array(
                    array('method' => 'popup', 'minutes' => 10),
                ),
            ),
        ));


        $service->events->insert($calendarId, $event);
        return $this->json([
            "message" => "Success"
        ]);

    }

    /**
     * @Route ("/oauth", name="oauth")
     * @param HttpClientInterface $httpClient
     * @return JsonResponse
     * @throws Google_Exception
     * @throws TransportExceptionInterface
     */
    public function oauth(HttpClientInterface $httpClient, Request $request): JsonResponse
    {
        if ($request->get("code")) {

            $client = new Google_Client();
            $client->setAuthConfig("client_secret.json");
            $client->addScope(Google_Service_Calendar::CALENDAR);
            $token = $client->fetchAccessTokenWithAuthCode($request->get("code"));

            return new JsonResponse([
                "token" => $token
            ]);
        }
        $rurl = "http://localhost/src/Client/objective.html";
        $client = new Google_Client();
        $client->setAuthConfigFile("client_secret.json");
        $client->setRedirectUri($rurl);
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $guzzleClient = new Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
        $client->setHttpClient($guzzleClient);


        $auth_url = $client->createAuthUrl();
        $filtered_url = filter_var($auth_url, FILTER_SANITIZE_URL);


        return new JsonResponse([
            "AuthURL" => $filtered_url
        ]);
    }


}
