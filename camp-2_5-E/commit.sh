#!/bin/bash
svn up data html
echo "svn add data/ html/ " 
svn add data html --force
echo "svn ci data/ html/"
#svn ci -m "auto commit by watch" data/ html/ 
svn ci -m "" data/ html/ 



