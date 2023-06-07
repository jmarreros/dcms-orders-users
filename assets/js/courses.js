// For manage by-courses view

var j = jQuery.noConflict();

var vmcourses = new Vue({
    el: '#courses-orders',
    data: {
        results: [],
        status: 0,
        loading: true,
    },
    created: function () {
        this.loadData()
    },
    methods: {
        reloadButton() {
            this.loadData()
        },
        loadData() {
            this.loading = true
            j.ajax({
                type: "post",
                url: dcmsCoursesOrders.ajaxurl,
                dataType: 'json',
                data: {
                    action: 'dcms_ajax_list_courses_orders',
                    nonce: dcmsCoursesOrders.nonce,
                },
                success: function (res) {
                    console.log(res)

                    if (res.status == 0) {
                        return
                    }

                    vmcourses.loading = false
                    vmcourses.results = res.data
                    vmcourses.status = res.status
                }
            })
        }
    }
});


// Drop Down Menu
function dropFlexiblePayment(event) {
    document.getElementById("drop-flexible-payment").classList.toggle("show");
}

// Close the dropdown menu if the user clicks outside of it
window.onclick = function (event) {
    if (!event.target.matches('.btn-drop') && !event.target.matches('.fa-caret-down')) {
        let dropdowns = document.getElementsByClassName("dropdown-content");
        let i;
        for (i = 0; i < dropdowns.length; i++) {
            let openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}
