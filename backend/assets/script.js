var j = jQuery.noConflict();

var vmreport = new Vue({
    el: '#report-courses',
    data: {
        results: [],
        status: 0,
        loading: true,
    },
    created: function(){
        this.loadData()
    },
    methods: {
        loadData() {
            this.loading = true
            j.ajax({
                type:"post",
                url: dcmsReport.ajaxurl,
                dataType: 'json',
                data: {
                    action : 'dcms_ajax_courses_report',
                    nonce : dcmsReport.nonce,
                    dstart: j('#dstart').val(),
                    dend: j('#dend').val(),
                    tcourse: j('#tcourse').val()
                },
                success:function(res){
                    vmreport.loading = false
                    vmreport.results = res.data
                }
            })
        },
        onSubmit(){
            this.loadData()
        },
        format_date($str_date){
            return new Date($str_date).toLocaleDateString('es-ES');
        }
    },
})



// var j = jQuery.noConflict();

// var vmorders = new Vue({
//     el: '#orders-user',
//     data: {
//         results: [],
//         status: 0,
//         page : 1,
//         loading: true,
//         totalPages: 0
//     },
//     created: function(){
//             this.loadData(this.page)
//     },
//     methods: {
//         loadData(page) {
//             this.loading = true
//             j.ajax({
//                 type:"post",
//                 url: dcmsOrders.ajaxurl,
//                 dataType: 'json',
//                 data: {
//                     action : 'dcms_ajax_list_orders',
//                     nonce : dcmsOrders.nonce,
//                     page
//                 },
//                 success:function(res){
//                     if ( res.status == 0 ) {
//                         console.log(res)
//                         return
//                     }

//                     vmorders.loading = false
//                     vmorders.results = res.data
//                     vmorders.status = res.status
//                     vmorders.totalPages = res.total_pages
//                 }
//             })
//         },
//         nextPage(){
//             this.page++
//             this.loadData(this.page)
//         },
//         prevPage(){
//             this.page--
//             this.loadData(this.page)
//         }
//     }
// })

