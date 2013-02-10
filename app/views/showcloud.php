<!DOCTYPE html>
<html>
<head>
    <title>Email Word Cloud</title>
    <link rel="stylesheet" type="text/css" href="/css/jqcloud.css" />
    <link rel="stylesheet" type="text/css" href="/css/iThing-min.css"/>
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="/css/bootstrap-responsive.min.css" />

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
					    wordArr = data.words.map(function(d){
		    				return {text : d.text, weight: d.weight, link : "#"};
		    			});
		    			$("#wordCount").text(data.word_count);
		    			$("#queryTime").text(data.query_time);
		    			$("#emailCount").text(data.email_count);
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
			dataObj.getWords();
		});
    	
    	$(document).on("click",".jqcloud a", function(e){
    		e.preventDefault();
    		dataObj.words.push($(this).text());
    		$(".wordsHeading").text(dataObj.words.join('+'));
    		dataObj.getWords();
    	});

    	$("#userText").keypress(function(e){
    		// e.preventDefault();
    		if(e.which==13){
    			dataObj.text = $("#userText").val();
    			dataObj.getWords();
    		}
    	});

    	$("#refresh").click(function(e){
    		e.preventDefault();
    		dataObj.words=[];
    		dataObj.startTime = null;
    		dataObj.endTime = null;
    		dataObj.text = null;
    		dataObj.getWords();
    	})
    	
	});
</script>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="span12 well well-small">
				<h1 class="title" style="text-align:center">Gist</h1>
			</div>
		</div>
		<div class="row">
			<div class="span9">
				<div class="row">
					<div class="span9">
						<div style="height:15px;margin">
							<div id="slider"></div>
						</div>
					</div>
					<div class="span9">
					</div>
					<div class="span9">
					    <div id="example" style="width: 800px; height: 500px;"></div>
					</div>
				</div>
			</div>
			<div class="span3">
				    <div class="input-prepend">
				      <span class="add-on"><i class="icon-user"></i></span>
				      <input class="input" id="userText" type="text">
				      <!-- <button class="btn" type="button">Search</button> -->
				    </div>
				    <table class="table striped">
				    	<tr>
				    		<td>Query Time</td>
				    		<td id="queryTime"></td>
				    	</tr>
				    	<tr>
				    		<td>Email Count</td>
				    		<td id="emailCount"></td>
				    	</tr>
				    	<tr>
				    		<td>Word Count</td>
				    		<td id="wordCount"></td>
				    	</tr>
				    </table>
				    <a class="btn btn-primary" id="refresh" href="#"> <i class="icon-refresh icon-white"></i> Refresh</a>
			</div>
		</div>
		<div class="row">
			<div class="span12 well">
				<!-- <p style="text-align:center;">Thanks </p>  -->
			</div>
		</div>
	</div>
</body>
</html>