<html>
<head>
    <title>Email Word Cloud</title>
    <link rel="stylesheet" type="text/css" href="/css/jqcloud.css" />
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" />
    <script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="/js/d3.js"></script>
    <script type="text/javascript" src="/js/d3.layout.cloud.js"></script>
    <script type="text/javascript">
      /*!
       * Create an array of word objects, each representing a word in the cloud
       */
		var fill = d3.scale.category20();   

       function draw(words) {
			    d3.select("#example").append("svg")
				    .attr("width", 300)
			        .attr("height", 300)
			    	.append("g")
			        .attr("transform", "translate(150,150)")
			    	.selectAll("text")
			        .data(words)
			    	.enter().append("text")
			        .style("font-size", function(d) { return d.size + "px"; })
			        .style("font-family", "Impact")
			        .style("fill", function(d, i) { return fill(i); })
			        .attr("text-anchor", "middle")
			        .attr("transform", function(d) {
			          return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
			        })
			        .text(function(d) { return d.text; });
			  }

  	function getWords(callback) {
  		$.ajax({
  			type: "GET",
  			dataType: "json",
  			url : "/getwords",
  			success : function(data){
  				callback(data);
  			}
  		});
    }
  	var word_array = [
	    {text: "Lorem", weight: 15},
	    {text: "Ipsum", weight: 9, link: "http://jquery.com/"},
	    {text: "Dolor", weight: 6, html: {title: "I can haz any html attribute"}},
	    {text: "Sit", weight: 7},
	    {text: "Amet", weight: 5}
	      // ...as many words as you want
  	];

	$(function() {
    // When DOM is ready, select the container element and call the jQCloud method, passing the array of words as the first argument.
	    getWords(function(data){
	    	console.log("inside getwords callback");
	    	// var wordArr = [];
		    // $.each(data,function(index,obj){
		    // 	obj.link = "#";
		    // 	wordArr.push(obj);
		    // });
		    // console.log(wordArr);
		    // $("#example").jQCloud(wordArr);

		    var fontSize = d3.scale.log().range([10, 100]);

			 d3.layout.cloud().size([300, 300])
		      .words(data.map(function(d){
		      	return {text : d.text, size : d.weight};
		      }))
		      .rotate(function() { return 0; })
		      .font("Impact")
		      .fontSize(function(d) { return d.size; })
		      .on("end", draw)
		      .start();
		  });
});
</script>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="span12 well">
				<h1>Email Word Cloud</h1>
			</div>
		</div>
		<div class="row">
			<div class="span12">
				<h2 class="wordsHeading">All</h2>
			    <div id="example" style="width: 700px; height: 350px;"></div>
			</div>
		</div>
		<div class="row">
			<div class="span12 well">
				
			</div>
		</div>
	</div>
</body>
</html>