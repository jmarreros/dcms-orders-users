var j = jQuery.noConflict();

var vm = new Vue({
    el: '#orders-user',
    data: {
        results: [],
        status: 0,
        page : 1,
    },
    created: function(){
            this.loadData(this.page)
    },
    methods: {
        loadData(page) {

            j.ajax({
                type:"post",
                url: dcmsOrders.ajaxurl,
                dataType: 'json',
                data: {
                    action : 'dcms_ajax_list_orders',
                    nonce : dcmsOrders.nonce,
                    page
                },
                success:function(res){
                    if ( res.status == 0 ) console.log(res)

                    vm.results = res.data
                    vm.status = res.status
                }
            })
        }
    }
})





// (function($){


//     $( document ).ready(function() {

//         $.ajax({
// 			url : dcmsOrders.ajaxurl,
// 			type: 'post',
//             dataType: 'json',
// 			data: {
// 				action : 'dcms_ajax_list_orders',
//                 nonce : dcmsOrders.nonce,
// 				page: 1
// 			},
// 			beforeSend: function(){

// 			},
// 			success: function(res){
//                 // res = JSON.parse(res);
// 				console.log(res)
// 			}

// 		});
//     });


// })(jQuery);

