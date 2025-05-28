// Spinner
var spinner = function () {
    setTimeout(function () {
        if ($('#spinner').length > 0) {
            $('#spinner').removeClass('show');
        }
    }, 1);
};
spinner();

// Back to top button
$(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
        $('.back-to-top').fadeIn('slow');
    } else {
        $('.back-to-top').fadeOut('slow');
    }
});
$('.back-to-top').click(function () {
    $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
    return false;
});

// Initialize Owl Carousel
$('.owl-carousel').owlCarousel({
    loop: true,
    margin: 20,
    nav: true,
    dots: false,
    responsive: {
        0: {
            items: 1
        },
        576: {
            items: 2
        },
        768: {
            items: 3
        },
        992: {
            items: 4
        }
    }
});

// Initialize Lightbox
lightbox.option({
    'resizeDuration': 200,
    'wrapAround': true
});

// Add to cart animation
$('.add-to-cart').click(function(e) {
    e.preventDefault();
    var $btn = $(this);
    var $icon = $btn.find('i');
    
    // Add animation class
    $icon.addClass('fa-spin');
    
    // Submit the form
    var $form = $btn.closest('form');
    $.post($form.attr('action'), $form.serialize(), function(response) {
        // Remove animation class
        $icon.removeClass('fa-spin');
        
        // Show success message
        if (response.success) {
            // Update cart count
            if (response.cart_count > 0) {
                $('.cart-count').text(response.cart_count);
            }
            
            // Show notification
            var $notification = $('<div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert">' +
                'Sản phẩm đã được thêm vào giỏ hàng!' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                '</div>');
            $('body').append($notification);
            
            // Remove notification after 3 seconds
            setTimeout(function() {
                $notification.alert('close');
            }, 3000);
        }
    });
});

// Quantity input handling
$('.quantity-input').on('change', function() {
    var $input = $(this);
    var value = parseInt($input.val());
    
    if (value < 1) {
        $input.val(1);
    }
});

// Quantity buttons
$('.quantity-btn').click(function() {
    var $btn = $(this);
    var $input = $btn.siblings('.quantity-input');
    var value = parseInt($input.val());
    
    if ($btn.hasClass('decrease')) {
        if (value > 1) {
            $input.val(value - 1).trigger('change');
        }
    } else {
        $input.val(value + 1).trigger('change');
    }
}); 