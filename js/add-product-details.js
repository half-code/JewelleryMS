var count = 1;
var set = new Set();
var map = {};
document.onreadystatechange = function () {

    //alert( yourSelect.options[ yourSelect.selectedIndex ].value );
    if (document.readyState == "interactive") {

        /**
         * This code deals with the dynamic addition and deletion of the products in the product form
         */
        var index = 1;
        var deleteProduct;
        let addProduct = $('.add-product');
        addProduct.click(function (event) {
            count++;
            index++;
            generateNewProductEntry(index);
            initSelectizeOn('#category-'+index, "#product-"+index);
            adjustDeleteButtonVisibility(index);
        });
        generateNewProductEntry(index);
        adjustDeleteButtonVisibility(index);

        function adjustDeleteButtonVisibility(index) {
            deleteProduct = $('.delete-product');
            $('#delete-product-' + index).click(function () {
                if(count > 1)
                    count--;
                delete map['#category-'+index];
                setRateOfPurchaseHtml();
                removeProductEntry("#list-product-"+$(this).data('value'));
                adjustDeleteButtonVisibility();
            });
            if(count === 1){
                deleteProduct.hide();
            }else{
                deleteProduct.show();
            }
        }
        initSelectizeOn('#category-1', "#product-1");
    }
}

function generateNewProductEntry(index) {
    $('#list-of-products').append("<div class='form-row' id='list-product-"+index+"'>\n" +
        "        <div class='form-group col-md-3'>\n" +
        "            <select name='category_id[]' id='category-"+index+"' class='form-control' required>\n" +
        "                <option value=''>Select Category</option>\n" +
        "            </select>\n" +
        "        </div>\n" +
        "        <div class='form-group col-md-5'>\n" +
        "            <select name='product_id[]' id='product-"+index+"' class='form-control' required>\n" +
        "                <option value=''>Select Product</option>\n" +
        "            </select>\n" +
        "        </div>\n" +
        "        <div class='form-group col-md-2'>\n" +
        "            <input type='number' class='form-control' name='product_quantity[]' id='product_quantity-"+index+"' required>\n" +
        "        </div>\n" +
        "        <button id='delete-product-" + index + "' class='btn btn-danger delete-product' role='button' type='button' data-value='"+index+"'><i class='fa fa-trash'></i></button>\n" +
        "    </div>");
}
function removeProductEntry(id) {
    $(id).remove();
}
/**
 *
 * @param select_obj
 * @param url
 * @param alertText
 */
function loadCategory(select_obj, url, alertText = ""){
    var xhr;
    select_obj.load(function (callback) {
        xhr && xhr.abort();
        xhr = $.post({
            url: url,
            data: {op: 'select'},
            success: function (result) {
                let res = JSON.parse(result);
                if($.isEmptyObject(res)){
                    if(alertText !== "")
                        alert(alertText);
                    return;
                }
                callback(res);
            },
            error: function () {
                callback();
            }
        });
    });
}

function setRateOfPurchaseHtml() {
    var str = "";
    set.clear();
    for (let key in map){
        set.add(JSON.stringify(map[key]));
    }
    set.forEach(function (value1, value2, set) {
        var value = JSON.parse(value1);
        str += "<div class='form-group col-md-12'>\n" +
            "            <label for='rate_of_purchase' data-toggle='tooltip' data-placement='right' title='' >Rate of purchase for " +  value["text"] + "<i class='fa fa-question-circle'></i></label>\n" +
            "            <div class='input-group'>\n" +
            "                <input type='number' class='form-control' name='"+  value['text'] + "'id='rate_of_purchase' placeholder='Enter Rate of purchase' aria-describedby='per-gm' required min='0'>\n" +
            "                <div class='input-group-append'>\n" +
            "                    <span class='input-group-text' id='per-gm'>gm's</span>\n" +
            "                </div>\n" +
            "            </div>\n" +
            "        </div>";
    });
    $('#rate-of-purchase').html(str);
}
/**
 * Selectize.js
 * this code will fetch all the products belonging to a particular category selected in the category selectize field.
 * It makes an ajax call and loads all the products
 * @param category_selector
 * @param product_selector
 */
function initSelectizeOn(category_selector, product_selector) {
    var xhr;
    var select_category, $select_category;
    var select_product, $select_product;
    $select_category = $(category_selector).selectize({
        valueField: 'category_id',
        labelField: 'category_name',
        searchField: 'category_name',
        onChange: function (value) {
            if (!value.length) return;
            map[category_selector] = {
                value : value,
                text: this.getItem(value)[0].innerHTML
            };
            setRateOfPurchaseHtml();
            select_product.disable();
            select_product.clear();
            select_product.clearOptions();
            select_product.load(function (callback) {
                xhr && xhr.abort();
                xhr = $.post({
                    url: "query-redirect.php?query=product",
                    data: {op: 'select', category_id: value},
                    success: function (result) {
                        select_product.enable();
                        let res = JSON.parse(result);
                        if($.isEmptyObject(res)){
                            alert("No Products Available");
                            select_product.disable();
                            return;
                        }
                        callback(res);
                    },
                    error: function () {
                        callback();
                    }
                });
            });
        },
        onItemRemove: function (value, item) {
            select_product.disable();
            select_product.clear();
            select_product.clearOptions();
        },
    });

    $select_product = $(product_selector).selectize({
        valueField: 'product_id',
        labelField: 'product_name',
        searchField: 'product_name',
    });
    select_product = $select_product[0].selectize;
    select_category = $select_category[0].selectize;

    select_product.disable();
    /**
     * loading categories in category selectize
     */
    loadCategory(select_category,"query-redirect.php?query=category","No Categories Available");
}