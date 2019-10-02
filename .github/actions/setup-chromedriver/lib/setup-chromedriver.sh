#!/bin/bash

set -eo pipefail

sudo apt-fast install -y build-essential debconf-utils screen google-chrome-stable
sudo apt-fast install -y unzip xvfb autogen autoconf libtool pkg-config nasm libgconf-2-4
wget -c -nc --retry-connrefused --tries=0 http://chromedriver.storage.googleapis.com/2.43/chromedriver_linux64.zip
unzip -o -q chromedriver_linux64.zip
sudo mv chromedriver /usr/local/bin/chromedriver
