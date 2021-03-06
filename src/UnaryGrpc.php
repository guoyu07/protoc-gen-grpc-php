<?php
namespace Lv\Grpc;

use Google\Protobuf\Internal\Message;

trait UnaryGrpc
{
    private $uri_method = [];
    private $not_found_service;

    private function doRequest(Session $session)
    {
        $data = substr($session->getBody(), 5);

        try {
            $service = $this->getService($session->getUri());

            /** @var Message $message */
            $message = $service($session, $data);

            $grpc_status = $session->getStatus();
            $grpc_message = $session->getMessage();
            if ($grpc_message) {
                $session->setMetadata('grpc-message', $grpc_message);
            }

            if ($grpc_status === Status::OK) {
                $content_type = $session->getMetadata('content-type');
                if ($content_type === 'application/grpc+json') {
                    $data = $message->serializeToJsonString();
                } else {
                    $data = $message->serializeToString();
                }
                $session->setMetadata('content-type', $content_type);
                $session->end(Status::OK, pack('CN', 0, strlen($data)).$data);
            } else {
                $session->end($grpc_status);
            }
        } catch (GPBDecodeException $e) {
            $session->end(Status::INVALID_ARGUMENT);
        } catch (Throwable $t) {
            // TODO more error code
            $session->end(Status::INTERNAL);
        }
    }

    private function getService(string $uri)
    {
        if (isset($this->uri_method[$uri])) {
            return $this->uri_method[$uri];
        }

        if (!isset($this->not_found_service)) {
            $this->not_found_service = new NotFoundService;
        }

        return $this->not_found_service;
    }

    public function addService(Service $service)
    {
        foreach ($service->getMethods() as $uri => $method) {
            $this->uri_method[$uri] = [$service, $method];
        }
    }
}
