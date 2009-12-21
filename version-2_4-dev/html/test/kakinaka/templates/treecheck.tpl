<!doctype html public "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
<title>Yahoo! UI Library - Tree Control</title>
<link rel="stylesheet" type="text/css" href="css/screen.css">
</head>
  
<body onload="treeInit()">

<link rel="stylesheet" type="text/css" href="css/check/tree.css">

<div id="pageTitle">
	<h3>Tree Control</h3>
</div>

<script type="text/javascript" src="js/yahoo/yahoo.js" ></script>

<!-- The following are required for the logger -->
<script type="text/javascript" src="js/event/event.js"></script>
<script type="text/javascript" src="js/dom/dom.js"></script>
<script type="text/javascript" src="js/logger/logger.js"></script>
<!-- End logger reqs -->

<script type="text/javascript" src="js/treeview/treeview-debug.js" ></script>

<style type="text/css">

/* logger default styles */
/* font size is controlled here: default 77% */
#yui-log {position:absolute;top:1em;right:1em;font-size:77%;text-align:left;}
/* width is controlled here: default 31em */
.yui-log {background-color:#AAA;border:1px solid black;font-family:monospace;z-index:9000;}
.yui-log p {margin:1px;padding:.1em;}
.yui-log button {font-family:monospace;}
.yui-log .yui-log-hd {padding:.5em;background-color:#575757;color:#FFF;}
/* height is controlled here: default 20em*/
.yui-log .yui-log-bd {width:100%;height:20em;background-color:#FFF;border:1px solid gray;overflow:auto;}
.yui-log .yui-log-ft {margin-top:.5em;margin-bottom:1em;}
.yui-log .yui-log-ft .yui-log-categoryfilters {}
.yui-log .yui-log-ft .yui-log-sourcefilters {width:100%;border-top:1px solid #575757;margin-top:.75em;padding-top:.75em;}
.yui-log .yui-log-btns {position:relative;float:right;bottom:.25em;}
.yui-log .yui-log-filtergrp {margin-right:.5em;}
.yui-log .info {background-color:#A7CC25;} /* A7CC25 green */
.yui-log .warn {background-color:#F58516;} /* F58516 orange */
.yui-log .error {background-color:#E32F0B;} /* E32F0B red */
.yui-log .time {background-color:#A6C9D7;} /* A6C9D7 blue */
.yui-log .window {background-color:#F2E886;} /* F2E886 tan */

</style>


<div id="container">
<img class="ylogo" src="img/logo.gif" alt="" />
  <div id="containerTop">
    <div id="header">
      <h1>
      
      </h1>
      <h4>&nbsp;</h4>
    </div>
    <div id="main">

<div id="rightbar">

<div id="rightBarPad">
<h3>Examples</h3>

<div id="linkage">
<ul>
<li><a href="default.html?mode=dist">Default tree widget</a></li>
<li><a href="dynamic.html?mode=dist">Dynamic load</a></li>


<li><a href="folders.html?mode=dist">Folder view</a></li>
<li><a href="menu.html?mode=dist">Menu</a></li>
<li><a href="html.html?mode=dist">HTML node</a></li>
<li><a href="multi.html?mode=dist">Multiple trees, different styles</a></li>
<li><a href="check.html?mode=dist">Task list</a></li>
<li><a href="anim.html?mode=dist">Fade animation</a></li>
</ul>

</div> 


    <script type="text/javascript">
    //<![CDATA[
    YAHOO.example.logApp = function() {
        var divId;
        return {
            init: function(p_divId, p_toggleElId, p_clearElId) {
                divId = p_divId
            },

            onload: function() {
                if (YAHOO.widget.Logger) {
                    new YAHOO.widget.LogReader( "logDiv", { height: "400px" } );
                }
            }
        };
    } (); 

    YAHOO.util.Event.on(window, "load", YAHOO.example.logApp.onload);

    //]]>
    </script>

    <div id="logDiv"></div>

</div>

</div>

<script type="text/javascript" src="js/TaskNode.js"></script>

<form name="mainForm" action="<!--{$smarty.server.PHP_SELF|escape}-->" method="post" >
  <div id="content">
	<div class="newsItem">
	  <h3>Task List</h3>
	  <div id="expandcontractdiv">
		<a href="javascript:tree.expandAll()">Expand all</a>
		<a href="javascript:tree.collapseAll()">Collapse all</a>
		<a href="javascript:checkAll()">Check all</a>
		<a href="javascript:uncheckAll()">Uncheck all</a>
	  </div>
	  <div id="treeDiv1"></div>
	  <div id="treeDiv2"></div>
	</div>
  </div>
      <div id="footerContainer">
        <div id="footer">
          <p>&nbsp;</p>
        </div>
      </div>
    </div>
  </div>
</div>

<input type="submit" id="showButton" value="subm"/>

</form>
<script type="text/javascript">

	var tree;
	var nodes = new Array();
	var nodeIndex;
	
	function treeInit() {
		buildRandomTextNodeTree();
	}
	
	function buildRandomTextNodeTree() {
	
		tree = new YAHOO.widget.TreeView("treeDiv1");
		tree2 = new YAHOO.widget.TreeView("treeDiv2");
		
		for (var i = 0; i < Math.floor((Math.random()*4) + 3); i++) {
			var tmpNode = new YAHOO.widget.TaskNode("kakinaka-" + i, tree.getRoot(), false);
            tmpNode.onCheckClick = onCheckClick;
			buildRandomTextBranch(tmpNode);
		}
		tree.draw();
        // tree.checkClickEvent.subscribe(onCheckClick);
	}

	var callback = null;

	function buildRandomTextBranch(node) {
		if (node.depth < 5) {
			YAHOO.log("buildRandomTextBranch: " + node.index);
			for ( var i = 0; i < Math.floor(Math.random() * 4) ; i++ ) {
				var tmpNode = new YAHOO.widget.TaskNode(node.label + "-" + i, node, false);
                tmpNode.onCheckClick = onCheckClick;
				buildRandomTextBranch(tmpNode);
			}
		} else {
		    // tmpNode = new YAHOO.widget.TaskNode(node.label + "-" + i, node, false, true);
        }
	}
    // function onCheckClick(eventType, args, tree) {
    //var node = args[0];
    function onCheckClick(eventType, args, tree) {
        var node = this;
        YAHOO.log(node.label + " check was clicked, new state: " + 
                node.checkState);
    }

    function showTreeState() {
        var out = [];
        for (var i in tree._nodes) {
            var n = tree._nodes[i];
            if (n && "undefined" != typeof n.checkState) {
                out.push(n.data + ": " + n.checkState);
            }
        }

        alert(out.join("\n"));
    }

    function checkAll() {
        var topNodes = tree.getRoot().children;
        for(var i = 0; i <= topNodes.length; ++i) {
            topNodes[i].check();
        }
    }

    function uncheckAll() {
        var topNodes = tree.getRoot().children;
        for(var i = 0; i <= topNodes.length; ++i) {
            topNodes[i].uncheck();
        }
    }

    function showJSON() {
        alert(JSON.stringify(tree._nodes));
    }

    //YAHOO.util.Event.on("showButton", "click", showTreeState);
    //YAHOO.util.Event.on("showButton", "click", showJSON);

</script>

  </body>
</html>
 
