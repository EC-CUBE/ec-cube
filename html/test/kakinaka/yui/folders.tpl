<!doctype html public "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
<title>Yahoo! UI Library - Tree Control</title>
<link rel="stylesheet" type="text/css" href="yui/css/screen.css">
</head>
  
<body onload="treeInit()">

<link rel="stylesheet" type="text/css" href="yui/css/folders/tree.css">

<div id="pageTitle">
	<h3>Tree Control</h3>
</div>

<script type="text/javascript" src="js/yahoo/yahoo.js" ></script>

<!-- The following are required for the logger -->
<script type="text/javascript" src="js/event/event.js"></script>
<script type="text/javascript" src="js/dom/dom.js"></script>
<script type="text/javascript" src="js/logger/logger.js"></script>
<!-- End logger reqs -->

<script type="text/javascript" src="js/treeview/treeview.js" ></script>

<div id="content">
	<form name="form_tree" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
	<input type="hidden" name="now_date" value="">
		<div class="newsItem">
			<h3>Folders</h3>
			<p></p>
			<div id="expandcontractdiv">
				<a href="javascript:tree.expandAll()">Expand all</a>
				<a href="javascript:tree.collapseAll()">Collapse all</a>
			</div>
			<div id="treeDiv0"></div>
			<div id="treeDiv1"></div>
			<div id="treeDiv2"></div>
			<div id="treeDiv3"></div>
			<div id="treeDiv4"></div>
		</div>
	</form>
</div>

<div id="footerContainer">
	<div id="footer">
		<p>&nbsp;</p>
	</div>
</div>


<script type="text/javascript">

	var tree;
	var nodes = new Array();
	var nodeIndex;
	
	function treeInit() {
		//buildRandomTextNodeTree();
	}
	
	function buildRandomTextNodeTree() {
		tree = new YAHOO.widget.TreeView("treeDiv1");

		for (var i = 0; i < <!--{$tpl_days}-->; i++) {
			var tmpNode = new YAHOO.widget.TextNode("unko-" + i, tree.getRoot(), false);
			buildRandomTextBranch(tmpNode);
		}

		tree.draw();
	}

	var callback = null;

	function buildRandomTextBranch(node) {
	
		if (node.depth < 4) {
			for ( var i = 0; i < Math.floor(0.6 * 4) ; i++ ) {
				var tmpNode = new YAHOO.widget.TextNode(node.label + "-" + i, node, false);
				buildRandomTextBranch(tmpNode);
			}
		}
	}
	
	
// �ĥ꡼�ǡ���
var data = <!--{$data}-->

//�������鲼�Ͽ���ʤ��Ƥ�OK
//(namespace�ϻȤ�ʤ��Ƥ�ư��ޤ�)
YAHOO.namespace('treefolder');//�������ޥ��������ؿ��ʤ��Ѥ�̾�����֤��Ѱդ��Ƥ����ޤ�
YAHOO.treefolder.tree = function(tree,data) {
	this.data = data;
	this.tree = tree;

	//Tree���� 
	this.mkTree = function (oj,node){
		for(var i in oj){
			if(typeof oj[i] != "number" && i != "_href"){
				if(typeof oj[i]["_index"] == "number"){
					var tmpNode = new YAHOO.widget.TextNode("" + i, node, false, oj[i]["_index"]);
				}else{
					var tmpNode = new YAHOO.widget.TextNode("" + i, node, false);
				}
				
				if(typeof oj[i] == "string")tmpNode.href= oj[i];
				if(typeof oj[i] == "object"){
					if(typeof oj[i]["_href"] == "string")tmpNode.href= oj[i]["_href"];
					if(oj[i]["_open"]==1)tmpNode.expand();
					this.mkTree(oj[i],tmpNode);
				}
			}
		}
		tree.draw();
	}
}

//�����
YAHOO.treefolder.treeIni = function(){

	var tree = new YAHOO.widget.TreeView("treeDiv1");//treeDiv1��ɽ������DIV��ID̾�Ǥ�
	test1 = new YAHOO.treefolder.tree(tree,data);//������Tree�ǡ������Ϥ��ޤ�
	test1.mkTree(test1.data, tree.getRoot());
}

//�ڡ����ɤ߹��߸�˥������ޥ����ؿ�YAHOO.treefolder.treeIni()��ư���ޤ�
YAHOO.util.Event.addListener(window, 'load', YAHOO.treefolder.treeIni);

</script>

</body>
</html>
 
