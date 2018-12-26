// Start for ProductRecommand



	function gennerateElemenrtProduct(productObj){
	
		var product = productObj;
		var brandLinkHref = baseurl+'brand/'+productObj.bran_BrandID;
		var productLinkHref;

		if(productObj.coup_Type=='Use'){
			product.coup_numUse = 'ใช้เเล้ว ' + productObj.coup_numUse;
			productLinkHref = baseurl+'promotion/'+productObj.coup_CouponID;
		}else if(productObj.coup_Type=='Buy'){
			product.coup_numUse = 'ขายเเล้ว ' +  productObj.coup_numUse;
			productLinkHref = baseurl+'shop/'+productObj.coup_CouponID;
		}else{
			product.coup_numUse = 'สมัครเเล้ว ' +  productObj.coup_numUse;
			productLinkHref = baseurl+'membercard/'+productObj.coup_CouponID;
		}
		var coup_PriceStr;
		if (productObj.coup_Price == 0) {
			coup_PriceStr = 'ฟรี!';
		}else{
			coup_PriceStr = `${productObj.coup_Price} ฿`;
		}

		var productSellStr;
		if (productObj.coup_Cost == 0) {
			productSellStr = ' ';
		}else{
			productSellStr = 'ลด ' + Math.round(((productObj.coup_Cost - productObj.coup_Price)/productObj.coup_Cost)*100) + '%';
		}
		
// debugger

// <div class="d-inline" ng-if="product.coup_Type == 'Buy'">ขายเเล้ว</div>
// 		              			<div class="d-inline" ng-if="product.coup_Type == 'Member'">สมัครเเล้ว</div>
// 		              			<div class="d-inline" ng-if="product.coup_Type == 'Use'">ใช้เเล้ว</div>
		

	
	//productObj.coup_Description = productObj.coup_Description.substr(0,30); /*จำกัดความยาว String*/
	
	return	`
			<div class="product productShowHome mt-4 mb-3">
				<div class="card shadow mb-3 mt-3 " style="max-width: 180rem;" >
					<a href="${brandLinkHref}">
						<img class="rounded-circle shadow-sm img-responsive logo-brand border border-secondary bg-light" src="upload/${product.path_logo}${product.logo_image}">
	            	</a>
	            	<a href="${productLinkHref}">
		            	<img class="card-img-top" src="upload/${product.coup_ImagePath+product.coup_Image}" >
		            	<div class="text-dark" ng-click='lookup("coup",product.coup_CouponID,product.coup_Type)'>
			          		<div class="card-title h5 bold m-1 setHeightCardHeadText ">
			          			${product.coup_Name}
			          		</div>
				              <div class="row m-1">
				              		<div class="text-right col-12 ">
				              			<div class="d-inline h6 regular pr-2 text-gray1">
				              				<small>
				              					${productSellStr}
				              				</small>
			              				</div>
				              			<div class="d-inline h4 medium text-danger">
				              				${coup_PriceStr}
				              			</div>
				              		</div>
		              		</div>
		              		<!-- <hr class="my-0 mx-3"> -->
				            <div class="row m-1 mt-2" style="font-size: 0.3rem;">

				              		<div class="col-12 text-right text-gray1">
				              			<div class="d-inline">${product.coup_numUse}</div>
				              		</div>
			              	</div>
			            </div>
		            </a>
		        </div>
	        </div>`;

	// return ' '+
	// 	'<div class="productHover w-100"> '+
	// 		'<div class="card mt-4 border border-secondary" style="max-width: 180rem; " >  '+
	// 			'<a href="'+brandLinkHref+'">  '+
 //  					'<img class="rounded-circle shadow-sm img-responsive logo-brand border border-secondary"  '+
 //    					'src="upload/'+productObj.path_logo+productObj.logo_image+'"> '+
 //    			'</a> '+
	// 			'<a href="'+productLinkHref+'">  '+
 //    				'<img class="card-img-top" src="upload/'+productObj.coup_ImagePath+productObj.coup_Image+'" > '+
 //  					'<div class="text-dark"> '+
	// 		          		'<div class="card-title h5 bold m-1 setHeightCardHeadText">'+productObj.coup_Name+'</div> '+
	// 			              '<div class="row m-1"> '+
	// 			              		'<div class="text-right col-12 "> '+
	// 			              			'<div class="d-inline h6 regular pr-2"> '+
	// 			              				// '<small> '+
	// 			              				// 	'ลด Math.round(((productObj.coup_Cost - productObj.coup_Price)/productObj.coup_Cost)*100) '+
	// 			              				// 	'% '+
	// 			              				// '</small> '+
	// 		              				'</div> '+
	// 			              			'<div class="d-inline h4 medium text-danger">'+ productObj.coup_Price + '</div> '+
	// 			              		'</div> '+
	// 	              		'</div> '+
	// 			            '<div class="row m-1 mt-2" style="font-size: 0.3rem;"> '+
	// 		              		'<div class="col-12 text-right"> '+
	// 		              			'<div class="d-inline" >'+productObj.coup_numUse+'</div> '+
	// 		              			// '<div class="d-inline" ng-if="product.coup_Type == 'Buy'">ขายเเล้ว</div> '+
	// 		              			// '<div class="d-inline" ng-if="product.coup_Type == 'Member'">สมัครเเล้ว</div> '+
	// 		              			// '<div class="d-inline" ng-if="product.coup_Type == 'Use'">ใช้เเล้ว</div> '+
	// 		              			'<div class="d-inline"> '+
	// 		              				' '+
	// 		              			'</div> '+
	// 		              		'</div> '+
	// 		              	'</div> '+
	// 		            '</div> '+
 //    			'</a> '+
	//     	'</div> '+
	//     '</div> ';




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
	  			crossDomain: true,
			    url: baseurl + 'dashboard/get_product_limit',
			    type: 'GET',
			    dataType: 'json',
			    contentType: "application/json;charset=utf-8",
		}).done(function(respone,status){
			// console.log(respone,status)
	  		// console.log($)

			if(status == "success") {
				resolve(respone);
			}else{
				reject('error getDB');
			} 
		  	
		});
	  })
	}
//End for ProductRecommand
// console.log(baseurl + 'dashboard/get_product_limit')


// Start for BrandRecommand
	function gennerateElemenrtBrand(brandObj){
		// console.log(brandObj)
		return '<a href="'+baseurl+'brand/'+brandObj.brand_id+'"> '+
				'<img src="upload/'+brandObj.path_logo+brandObj.logo_image+'" '+
				'class="rounded img-responsive home_brand" alt="'+brandObj.name+'">'+
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
	// console.log('sssssssssssssssss')
	 // console.log(respone)
  return foreachDatasAnd(respone)
}).then(function(arrayHtml){
 	var swiper = new Swiper('.swiper-container.swiperProducts', {
	    slidesPerView: 3,
	    spaceBetween: 30,
	    breakpointsInverse: true,
	    slidesPerGroup: 1,
	       // centeredSlides: true,
		breakpoints: {
		  	1: {
		      slidesPerView: 1,
		      slidesPerGroup: 1,
		      spaceBetween: 10
		    },
		    1080: {
		      slidesPerView: 3,
		      slidesPerGroup: 2,
		      spaceBetween: 30
		    }
		},
		// autoplay: {
		//     delay: 3000,
		// },
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
	      slidesPerView: 4,
	      slidesPerGroup: 3,
	      spaceBetween: 10
	    },
	    1080: {
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



