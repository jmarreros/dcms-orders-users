var j = jQuery.noConflict();

var vmorders = new Vue({
    el: '#orders-user',
    data: {
        results: [],
        status: 0,
        page : 1,
        loading: true,
        totalPages: 0
    },
    created: function(){
            this.loadData(this.page)
    },
    methods: {
        loadData(page) {

            this.loading = true

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

                    vmorders.results = res.data
                    vmorders.status = res.status
                    vmorders.loading = false
                    vmorders.totalPages = res.total_pages
                }
            })
        },
        nextPage(){
            this.page++
            this.loadData(this.page)
        },
        prevPage(){
            this.page--
            this.loadData(this.page)
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

