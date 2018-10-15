// Start for ProductRecommand
	function gennerateElemenrtProduct(productObj){
	// console.log(productObj)
	return ' '+
		'<div class="productHover"> '+
			'<div class="card shadow mt-4" style="max-width: 180rem;" >  '+
				'<a href="'+baseurl+'/product/'+productObj.coup_CouponID+'">  '+
  					'<img class="rounded-circle shadow-sm img-responsive logo-brand"  '+
    					'src="upload/'+productObj.path_logo+productObj.logo_image+'"> '+
  					'<img class="card-img-top" src="upload/'+productObj.coup_ImagePath+productObj.coup_Image+'" > '+
  					'<div class="text-dark"> '+
        				'<h5 class="card-title text-left m-1">'+productObj.coup_Name+'</h5> '+
            			'<div class="row m-1"> '+
                		'<div class="col-4"></div> '+
                		'<div class="col-4 text-right" style="text-decoration: line-through;"><small>3000฿</small></div> '+
                		'<div class="col-4 text-right text-danger"><h5>500฿</h5></div> '+
        				'</div> '+
            			'<div class="row m-1 mt-2" style="font-size: 0.3rem;">      '+
		    			'<div class="col-3"><small > '+
			    			'<i class="fas fa-dollar-sign"></i> 5</small> '+
			    		'</div> '+
			    		'<div class="col-5 text-center text-warning " > '+
			    			'<i class="fa fa-star fa-xs"></i> '+
			    			'<i class="fa fa-star fa-xs"></i> '+
			    			'<i class="fa fa-star fa-xs"></i> '+
			    			'<i class="fa fa-star fa-xs"></i> '+
			    			'<i class="fa fa-star fa-xs"></i> '+
		    			'</div> '+
		    			'<div class="col-4 text-right"><small>ขายเเล้ว 20</small></div> '+
			    		'</div> '+
	    			'</div> '+
    			'</a> '+
	    	'</div> '+
	    '</div> ';
	}
	let foreachDatasAnd = function(respone){
	  return new Promise(function(resolve, reject) {
	  	if (Array.isArray(respone)){
	  		var arrayHtml = [];
	    	respone.forEach(function(item,index){
				// console.log(item,index+1,respone.length)
				var htmlReady = gennerateElemenrtProduct(item);
				arrayHtml.push(htmlReady);
				if(index+1==respone.length){
					resolve(arrayHtml);
				}
			});
		}else{
			reject('error foreachDatasAnd');
		}
	  	
	  })
	}


	let getProducts = function(){
	  return new Promise(function(resolve, reject) {
	  	$.ajax({
			    url: baseurl + 'dashboard/getdataLimit',
			    type: 'GET',
			    dataType: 'json',
			    contentType: "application/json;charset=utf-8",
		}).done(function(respone,status){
			// console.log(respone,status)
	  		console.log('hello')

			if(status == "success") resolve(respone)
		  	else reject('error getDB')
		});
	  })
	}
//End for ProductRecommand


// Start for BrandRecommand
	function gennerateElemenrtBrand(brandObj){
		// console.log(brandObj)
		return '<a href="'+baseurl+'brands'+brandObj.brand_id+'"> '+
				'<img src="upload/'+brandObj.path_logo+brandObj.logo_image+'" '+
				'class="rounded img-responsive home_brand shadow" alt="'+brandObj.name+'">'+
			'</a>';
	}

	let foreachBrandsAndCustom = function(respone){
	  return new Promise(function(resolve, reject) {
	  	if (Array.isArray(respone)){
	  		var arrayHtml = [];
	    	respone.forEach(function(item,index){
				// console.log(item,index+1,respone.length)
				var htmlReady = gennerateElemenrtBrand(item);
				arrayHtml.push(htmlReady);
				if(index+1==respone.length){
					resolve(arrayHtml);
				}
			});
		}else{
			reject('error foreachBrandsAndCustom');
		}
	  	
	  })
	}

	let getBrands = function(){
	  return new Promise(function(resolve, reject) {
	  	$.ajax({
			    url: baseurl + 'dashboard/getBrandRecommand',
			    type: 'GET',
			    dataType: 'json',
			    contentType: "application/json;charset=utf-8",
		}).done(function(respone,status){
			if(status == "success") resolve(respone)
		  	else reject('error getBrands')
		});
	  })
	}
//End for BrandRecommand

getProducts().then(function(respone) {
	// console.log(respone)
  return foreachDatasAnd(respone)
}).then(function(arrayHtml){
 	var swiper = new Swiper('.swiper-container.swiperProducts', {
      slidesPerView: 3,
      spaceBetween: 30,
      breakpointsInverse: true,
      slidesPerGroup: 1,
	  breakpoints: {
	  	1: {
	      slidesPerView: 1,
	      slidesPerGroup: 1,
	      spaceBetween: 30
	    },
	    768: {
	      slidesPerView: 3,
	      slidesPerGroup: 2,
	      spaceBetween: 30
	    }
	  },
      pagination: {
        el: '.swiper-pagination',
        type: 'fraction',
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      virtual: {
        slides: (function () {
			return arrayHtml;
        }()),
      },
    });
  // console.log(arrayHtml)
}).catch(function(error){
  console.log(error);
});

getBrands().then(function(respone) {
  return foreachBrandsAndCustom(respone)
}).then(function(arrayHtml){
 	var swiperBrand = new Swiper('.swiper-container.swiperBrands', {
      slidesPerView: 6,
      spaceBetween: 30,
      breakpointsInverse: true,
      slidesPerGroup: 1,
	  breakpoints: {
	  	1: {
	      slidesPerView: 3,
	      slidesPerGroup: 3,
	      spaceBetween: 10
	    },
	    768: {
	      slidesPerView: 6,
	      slidesPerGroup: 5,
	      spaceBetween: 30
	    }
	  },
      pagination: {
        el: '.swiper-pagination',
        type: 'fraction',
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      virtual: {
        slides: (function () {
			return arrayHtml;
        }()),
      },
    });
  // console.log(arrayHtml)
}).catch(function(error){
  console.log(error);
});



