#!/bin/bash

echo "svn add data/ html/ " 
svn add data html --force
echo "svn ci data/ html/"
svn ci -m "$1" data/ html/ 



