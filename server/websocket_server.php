<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require dirname(__DIR__) . '/vendor/autoload.php';

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $users;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->users = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        parse_str($conn->httpRequest->getUri()->getQuery(), $query);
        $username = $query['user'] ?? null;
        
        if ($username) {
            $this->users[$conn->resourceId] = $username;
            echo "New connection: {$username} ({$conn->resourceId})\n";
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        
        if ($data['type'] === 'message') {
            $sender = $this->users[$from->resourceId];
            $message = $data['message'];
            $timestamp = time();

            $dbPath = __DIR__ . '/../messages.db';
            $db = new PDO('sqlite:' . $dbPath);
            $stmt = $db->prepare("INSERT INTO messages (sender, message, timestamp) VALUES (?, ?, ?)");
            $stmt->execute([$sender, $message, $timestamp]);

            $response = json_encode([
                'type' => 'message',
                'sender' => $sender,
                'message' => $message,
                'timestamp' => $timestamp
            ]);

            foreach ($this->clients as $client) {
                $client->send($response);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);
        echo "Connection {$conn->resourceId} closed\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);

echo "WebSocket server running on port 8080\n";
$server->run();