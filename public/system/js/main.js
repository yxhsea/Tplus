/**
 * Crayola colors in JSON format
 * from: https://gist.github.com/jjdelc/1868136
 */

$(function () {
	
	$("#nope").keydown(function(){
		 $.ajax({
			  url: "http://localhost/antai/area/index/seachtitle",
			  data: {
                  title:$("#nope").val()
		      },
			  dataType: "json",
			  success:function(data){
	            	var colors= data.date;
	            	console.log(colors);
	            	$('#nope').autocompleter({
	        	        // marker for autocomplete matches
	        	        highlightMatches: true,
	        	        // object to local or url to remote search
	        	        source: colors,
	        	        // custom template
	        	        template: '<span>{{ label }}</span>(<span>{{ address }}</span>)',
	        	        // show hint
	        	        hint: true,
	        	        // abort source if empty field
	        	        empty: false,
	        	        // max results
	        	        limit: 10,
	        	        callback: function (value, index, selected) {
//	        	        	alert(selected.id);
	        	            if (selected) {
	        	                $('.icon').css('background-color', selected.hex);
	        	            }
	        	            $('#dis_id').val(selected.id) ;
	        	        }
	        	    });
	          }
		});
	});
});
