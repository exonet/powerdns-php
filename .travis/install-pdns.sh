#!/bin/sh

# Use the PowerDNS repo as source.
if [ "$PDNS_VERSION" = "41" ]; then
    sudo echo 'deb [arch=amd64] http://repo.powerdns.com/ubuntu xenial-auth-41 main' > /etc/apt/sources.list.d/pdns.list
fi

if [ "$PDNS_VERSION" = "42" ]; then
    sudo echo 'deb [arch=amd64] http://repo.powerdns.com/ubuntu xenial-auth-42 main' > /etc/apt/sources.list.d/pdns.list
fi

if [ "$PDNS_VERSION" = "43" ]; then
    sudo echo 'deb [arch=amd64] http://repo.powerdns.com/ubuntu xenial-auth-43 main' > /etc/apt/sources.list.d/pdns.list
fi

# Get the specific release and install.
curl https://repo.powerdns.com/FD380FBB-pub.asc | sudo apt-key add - && sudo apt-get update && sudo apt-get install pdns-server pdns-backend-mysql
sudo cp "$TRAVIS_BUILD_DIR/.travis/pdns.conf" /etc/powerdns/pdns.conf
sudo service pdns restart
