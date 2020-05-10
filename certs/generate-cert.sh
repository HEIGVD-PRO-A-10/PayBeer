#!/bin/bash

openssl genrsa -aes256 -out root.key 4096
openssl req -new -x509 -key root.key -out root.cert -days 3652 -sha256

openssl genrsa -aes256 -out paybeer.key 4096
openssl req -new -key paybeer.key -out paybeer.req
openssl x509 -req -in paybeer.req -out paybeer.cert -CA root.cert -CAkey root.key  -sha256 -CAcreateserial -days 365

openssl genrsa -aes256 -out beguin.key 4096
openssl req -new -key beguin.key -out beguin.req
openssl x509 -req -in beguin.req -out beguin.cert -CA root.cert -CAkey root.key  -sha256 -CAcreateserial -days 365
