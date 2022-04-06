var j = jQuery.noConflict();

var vm = new Vue({
    el: '#orders-user',
    data: {
        results: [],
        status: 0,
    },
    created: function(){
                this.loadData(2)
    },
    methods: {
        loadData: function (currentPage) {
            j.ajax({
                type:"post",
                url: dcmsOrders.ajaxurl,
                dataType: 'json',
                data: {
                    action : 'dcms_ajax_list_orders',
                    nonce : dcmsOrders.nonce,
                    page : currentPage
                },
                success:function(res){
                    console.log(res.data)
                    console.log(res.status)

                    vm.results = res.data
                    vm.status = res.status
                },
                error: function(error){
                    vm.results = 'Error';
                }
            });
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

