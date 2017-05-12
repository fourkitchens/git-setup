<?php

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;

class PivotalTracker {
  private $api_url = 'https://www.pivotaltracker.com/services/v5';
  private $api_token;
  private $project_id;

  public function __construct($api_token, $project_id) {
    $this->api_token = $api_token;
    $this->project_id = $project_id;
  }

  public function prepareSourceCommitMessage(array $content) {
    $message = array();
    $content += array(
      'message' => '',
      'author' => '',
      'commit_id' => '',
      'url' => '',
    );

    if ($this->getTicket($content['message'])) {
      $message = array(
        'source_commit' => $content,
      );
    }

    return $message;
  }

  public function getTicket($message) {
    $ticket = NULL;

    if ($ticket_number = $this->getTicketNumber($message)) {
      $client = new GuzzleClient();

      try {
        $response = $client->get("{$this->api_url}/projects/{$this->project_id}/stories/$ticket_number",
          array('headers' => $this->getHeaders()));
        $ticket = $response->json();
      }
      catch (GuzzleClientException $e) {
        if ($e->getCode() === 404) {
          throw new Exception("$ticket_number is not a valid ticket for project {$this->project_id}.");
        }
        throw $e;
      }
    }

    return $ticket;
  }

  public function getTicketNumber($message) {
    $ticket_number = 0;
    $matches = array();
    if (preg_match('/^\[#([0-9]{1,16})\]/', $message, $matches)) {
      if (!empty($matches[1])) {
        $ticket_number = (int) $matches[1];
      }
    }

    return $ticket_number;
  }

  public function getHeaders() {
    return array(
      'content-type' => 'application/json',
      'X-TrackerToken' => $this->api_token,
    );
  }

  public function sendCommit($message) {
    $client = new GuzzleClient();
    $client->post("{$this->api_url}/source_commits", array('headers' => $this->getHeaders(), 'body' => json_encode($message)));
  }
}
