# protoc-gen-grpc-php

grpc plugin for php.

## Installation

	go get github.com/lvht/protoc-gen-grpc-php

## Generate PHP SDK

run the following command to generate php sdk for helloworld.proto

	protoc --php_out=out --grpc-php_out=composer_name=grpc/hello:out ./helloworld.proto

you will get

```
out
├── composer.json                   -- make sdk as a composer package
├── GPBMetadata
│   └── Helloworld.php
└── Helloworld
    ├── AbstractGreeterService.php  -- generated service helper code
    ├── GreeterClient.php           -- generated service client code
    ├── Greeter.php                 -- service defined in proto will be generated as php interface
    ├── HelloReply.php              -- generated by protoc
    └── HelloRequest.php            -- generated by protoc
```

For **XXX** service define in proto, you will get a `XXX` interface. The `XXX`
interface has two implementation, `XXXClient` for client and `AbstractXXXServer`
for base server code.

Please see the `example/client.php` and `example/server.php` for more detail.

## Arguments

protoc-gen-grpc-php offer some arguments.

* composer_name
* client_trait
* require_name
* require_version