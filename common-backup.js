$(document).ready(function() {
/*  Redirection Cart  Page */
$('#accordion').accordion({autoHeight:false,collapsible:true,active:false});
$('#button-cart').bind('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: $('.product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\']:checked, .product-info input[type=\'checkbox\']:checked, .product-info select, .product-info textarea'),
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, information, .error').remove();
			
			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						$('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
					}
				}
			} 
			
			if (json['success']) {
				window.location.href="/checkout/cart";				
			}	
		}
	});
});
/* Currency */
$('#curr').on('change',function(){
		var id= $(this).val();
		$('input[name=\'currency_code\']').attr('value',id)
			
		$('#c').submit();
		});
		
/* menu active class*/
$('.MainMenu > ul > li > ul').find('li').on('mouseover',function(){
var id= $(this).attr('rel');
    $('#category-'+id+'> a').addClass('active-category');
});
$('.MainMenu > ul > li > ul').find('li').on('mouseout',function(){
var id= $(this).attr('rel');
    $('#category-'+id+'> a').removeClass('active-category');
});

/* Starting Responsive Main Menu */
	$(".MobileMenu a").click(function(){
		$(".MainMenuItem").slideToggle(500);
		if($(this).hasClass('active'))
			$(this).removeClass('active');
		else
			$(this).addClass('active');
	});
	$("span.MobsubMenuChild1").click(function(){
		rel=	$(this).attr('rel');
		$("#subc_"+rel).slideToggle(500);
		if($(this).hasClass('active'))
			$(this).removeClass('active');
		else
			$(this).addClass('active');
	});
		
	$("span.MobsubMenuChild2").click(function(){
	rel=	$(this).attr('rel');
			$("#sub_"+rel).slideToggle(500);
			if($(this).hasClass('active'))
				$(this).removeClass('active');
			else
				$(this).addClass('active');
	});
/* End Responsive Main Menu */

/* wishlist image*/
$('.wishlist').mouseover(function(){
	id=$(this).attr('rel');
	$("#wishlist-box-"+id).show();
});
$('.wishlist').mouseout(function(){
	id=$(this).attr('rel');

	$("#wishlist-box-"+id).hide();
});

$(document).on('mouseover','.imageHover',function(){
id=$(this).attr('rel');
	$("#imageHover-"+id).attr('src','/image/wishlist-icon-hvr.png');
});
$(document).on('mouseout','.imageHover',function(){
id=$(this).attr('rel');
	$("#imageHover-"+id).attr('src','/image/wishlist-icon.png');
});

/*guest newsletter*/

$('#news_sign_up').on('click',function(){
	var email		= $('#guest_email').val();
	var emailReg 	= /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	if(email=='')
	{
		alert('Please enter email');
		$('#guest_email').focus();
		return false;
	}
	else if(!emailReg.test(email))
	{
		alert('Please enter valid email');
		$('#guest_email').focus();
		return false;
	}
	$.ajax({
		url		: '/index.php?route=account/newsletter/guest',
		type	: 'POST',
		data	: 'email='+email,
		success : function(data)
		{
			alert(data);
		}
	})
});
	/* Search */
	$('.button-search').bind('click', function() {
		url = $('base').attr('href') + 'index.php?route=product/search';
				 
		var search = $('input[name=\'search\']').attr('value');
		
		if (search) {
			url += '&search=' + encodeURIComponent(search);
		}
		
		location = url;
	});
	
	$('#header input[name=\'search\']').bind('keydown', function(e) {
		if (e.keyCode == 13) {
			url = $('base').attr('href') + 'index.php?route=product/search';
			 
			var search = $('input[name=\'search\']').attr('value');
			
			if (search) {
				url += '&search=' + encodeURIComponent(search);
			}
			
			location = url;
		}
	});
	
	/* Ajax Cart */
	$('#cart > .heading a').live('click', function() {
		$('#cart').addClass('active');
		
		$('#cart').load('index.php?route=module/cart #cart > *');
		
		$('#cart').live('mouseleave', function() {
			$(this).removeClass('active');
		});
	});
	
	/* Mega Menu */
	$('#menu ul > li > a + div').each(function(index, element) {
		// IE6 & IE7 Fixes
		if ($.browser.msie && ($.browser.version == 7 || $.browser.version == 6)) {
			var category = $(element).find('a');
			var columns = $(element).find('ul').length;
			
			$(element).css('width', (columns * 143) + 'px');
			$(element).find('ul').css('float', 'left');
		}		
		
		var menu = $('#menu').offset();
		var dropdown = $(this).parent().offset();
		
		i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());
		
		if (i > 0) {
			$(this).css('margin-left', '-' + (i + 5) + 'px');
		}
	});

	// IE6 & IE7 Fixes
	if ($.browser.msie) {
		if ($.browser.version <= 6) {
			$('#column-left + #column-right + #content, #column-left + #content').css('margin-left', '195px');
			
			$('#column-right + #content').css('margin-right', '195px');
		
			$('.box-category ul li a.active + ul').css('display', 'block');	
		}
		
		if ($.browser.version <= 7) {
			$('#menu > ul > li').bind('mouseover', function() {
				$(this).addClass('active');
			});
				
			$('#menu > ul > li').bind('mouseout', function() {
				$(this).removeClass('active');
			});	
		}
	}
	
	$('.success img, .warning img, .attention img, .information img').live('click', function() {
		$(this).parent().fadeOut('slow', function() {
			$(this).remove();
		});
	});
	/* description */	
	$(".ProDescTab h3").click(function(){
	var rel= $(this).attr('rel');
		$('#'+rel).slideToggle(500);
		if($(this).hasClass('active'))
			$(this).removeClass('active');
		else
			$(this).addClass('active');
	});
	/* category */
	$("span.HomeCatChild").click(function(){
		rel=	$(this).attr('rel');
		$("#HomeSubLink_"+rel).slideToggle(500);
		if($(this).hasClass('active'))
			$(this).removeClass('active');
		else
			$(this).addClass('active');
	});
});
/* Back function */
$(function() {
$('#back').click(function() {
window.history.back();
return false;
});
});

function getURLVar(key) {
	var value = [];
	
	var query = String(document.location).split('?');
	
	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');
			
			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}
		
		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
} 

function addToCart(product_id, quantity) {
	quantity = typeof(quantity) != 'undefined' ? quantity : 1;

	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: 'product_id=' + product_id + '&quantity=' + quantity,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information, .error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['success']) {
				window.location.href="/checkout/cart";
			}	
		}
	});
}
function addToWishList(product_id,image_id) {
	$.ajax({
		url: 'index.php?route=account/wishlist/add',
		type: 'post',
		data: 'product_id=' + product_id,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information').remove();
						
			if (json['success']) {
				$('#'+image_id).html('<img class="imageHover" src="/image/wishlist-saved.png" />');				
				$('#wishlist-total').html(json['total']);
				
			}	
		}
	});
}

function addToCompare(product_id) { 
	$.ajax({
		url: 'index.php?route=product/compare/add',
		type: 'post',
		data: 'product_id=' + product_id,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information').remove();
						
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				
				$('.success').fadeIn('slow');
				
				$('#compare-total').html(json['total']);
				
				$('html, body').animate({ scrollTop: 0 }, 'slow'); 
			}	
		}
	});
}
