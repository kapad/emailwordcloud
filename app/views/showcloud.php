<html>
<head>
    <title>Email Word Cloud</title>
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="/css/jqcloud.css" />
    <!--<link rel="stylesheet" type="text/css" href="/css/classic-min.css"/>-->
    <link rel="stylesheet" type="text/css" href="/css/iThing-min.css"/>
    <script type="text/javascript" src="/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="/js/jquery-ui-1.8.16.custom.min.js"></script>
    <script type="text/javascript" src="/js/jquery.mousewheel.min.js"></script>
    <script type="text/javascript" src="/js/jQDateRangeSlider-withRuler-min.js"></script>
    <script type="text/javascript" src="/js/jqcloud-1.0.2.js"></script>
    <script type="text/javascript" src="/js/moment.min.js"></script>
    <script type="text/javascript">
      /*!
       * Create an array of word objects, each representing a word in the cloud
       */
    var dataObj = function(){
    	return {
    		words : [],
    		startTime : null,
    		endTime : null,
    		text : null,
    		getWords : function(){
    			options = {
    				type: "GET",
		  			dataType: "json",
		  			url : "/getwords",
		  			success : function(data) {
		  				var wordArr = [];
					    wordArr = data.map(function(d){
		    				return {text : d.text, weight: d.weight, link : "#"};
		    			});
		    			$("#example").html("");
					    $("#example").jQCloud(wordArr); 
		  			}
    			};
    			var params = {};
    			if(this.words.length!==0) params.words = this.words;
				if(Boolean(this.startTime) && Boolean(this.endTime)){
					console.log("inside start and end time");
					params.startTime = this.startTime;
					params.endTime = this.endTime;	
				}
				if(Boolean(this.text)) params.text = this.text;
				if(!jQuery.isEmptyObject(params)) options.data = {data:params};
    			$.ajax(options);
    		}
    	};
    }();

	$(function() {
    	$("#slider").dateRangeSlider({bounds: {
	    		min: new Date(2012, 1, 1),
			    max: new Date(2013, 1, 28)
	    	},
	    	defaultValues : {
	    		min : new Date(2013, 0, 1),
	    		max : new Date(2013, 1, 10)
	    	}
	    });

	    dataObj.getWords();

	    $("#slider").bind("userValuesChanged", function(e, data){
			console.log("Something moved. min: " + data.values.min + " max: " + data.values.max);
			dataObj.startTime = moment(data.values.min).format("YYYY-MM-DD");
			dataObj.endTime = moment(data.values.max).format("YYYY-MM-DD");
			console.log(dataObj.startTime);
			console.log(dataObj.endTime);
			dataObj.getWords();
		});

	    // getWords(function(data){
	    // 	var wordArr = [];
		   //  $.each(data,function(index,obj){
		   //  	obj.link = "#";
		   //  	wordArr.push(obj);
		   //  });
		   //  $("#example").jQCloud(wordArr);    	
	    // });

	    // function setRelatedWords(words){
	    // 	var wordArr = [];
	    // 	$.ajax({
	    // 		type:"GET",
	    // 		// dataType: "json",
	    // 		url : "/getwords",
	    // 		data : {data:words},
	    // 		success :function(data) {
	    // 			wordArr = data.map(function(d){
	    // 				return {text : d.text, weight: d.weight, link : "#"};
	    // 			});
	    // 			console.log(wordArr);
				 //    $("#example").html("");
		   //  		$("#example").jQCloud(wordArr);
	    // 		}
	    // 	});
	    // }
    // $("#example").jQCloud(word_array);    	
    	$(document).on("click",".jqcloud a", function(e){
    		e.preventDefault();
    		dataObj.words.push($(this).text());
    		$(".wordsHeading").text(dataObj.words.join('+'));
    		dataObj.getWords();
    	});
    	// $(document).o
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
				<div style="height:60px;">
					<div id="slider" ></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span12">
				<span class="wordsHeading">All</span>
			</div>
			<div class="span12">
			    <div id="example" style="width: 700px; height: 400px;"></div>
			</div>
		</div>
		<div class="row">
			<div class="span12 well">
				<p style="text-align:center;">Thanks </p> 
			</div>
		</div>
	</div>
</body>
</html>