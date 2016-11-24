#!/bin/bash

docker login -u="$QUAY_USERNAME" -p="$QUAY_PASSWORD" quay.io
docker tag processortranspose_app quay.io/keboola/processor-transpose:$TRAVIS_TAG
docker push quay.io/keboola/processor-transpose:$TRAVIS_TAG
