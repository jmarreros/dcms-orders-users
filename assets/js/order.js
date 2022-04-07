var j = jQuery.noConflict();

var vmorder = new Vue({
    el: '#order-user',
    data: {
        results: [],
        status: 0,
        loading: true
    },
    created: function(){
        this.loadData()
    },
    methods: {
        loadData() {
            this.loading = true
            j.ajax({
                type:"post",
                url: dcmsOrder.ajaxurl,
                dataType: 'json',
                data: {
                    action : 'dcms_ajax_order_detail',
                    nonce : dcmsOrder.nonce
                },
                success:function(res){

                    if ( res.status == 0 ) {
                        console.log(res)
                        return
                    }

                    console.log(res)

                    vmorder.loading = false
                    // vmorder.results = res.data
                    // vmorder.status = res.status
                }
            })
        }
    }
})

