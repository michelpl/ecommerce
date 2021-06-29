# Ecommerce Cart Api

[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/1954140-559ca720-0fdb-40f2-8a00-dba85e42b67e?action=collection%2Ffork&collection-url=entityId%3D1954140-559ca720-0fdb-40f2-8a00-dba85e42b67e%26entityType%3Dcollection%26workspaceId%3D884cf7ff-ca99-4231-944e-d47ac4babda5)

## Description

This is an E-commerce cart api that receives requests for cart updates

## Requirements

- Docker `20.10.5+`
- Docker compose `1.29.2+`

### Make sure the following ports are avaliable in your server

* Port `8080` for api
* Port `2121` for grpc client
* Port `50051` for grpc server


## Building the environment

#### Clone this repository

```bash
git clone https://github.com/michelpl/ecommerce-api
```

#### Clone this repository

```bash
git clone https://github.com/michelpl/ecommerce-api
```

#### Enter the project's devops folder

```bash
cd ecommerce-api/devops
```

#### Build

```bash
docker-compose up -d --build
```

After the building process, the main api wil be available on `http://localhost:8080

![usage](https://imgur.com/NDq1w0x.gif)

## Usage

Send a `[POST]` request for the `checkout` endpoint using the a list of products as payload in the request's body as on the following example

```shell
curl --location --request POST 'http://localhost:8080/api/v1/checkout' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--data-raw '{
    "products": [
        {
            "id": 3,
            "quantity": 1
        },
        {
            "id": 5,
            "quantity": 1
        },
        {
            "id": 2,
            "quantity": 3
        }
    ]
}'
```

![usage](https://imgur.com/yYIgaHX.gif)

### Postman collection

You can run the project collection in your [Postman]("https://www.postman.com/") app

[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/1954140-559ca720-0fdb-40f2-8a00-dba85e42b67e?action=collection%2Ffork&collection-url=entityId%3D1954140-559ca720-0fdb-40f2-8a00-dba85e42b67e%26entityType%3Dcollection%26workspaceId%3D884cf7ff-ca99-4231-944e-d47ac4babda5)

or

[Donwload](https://www.getpostman.com/collections/a391fac6619543eae84f) the collection's json


