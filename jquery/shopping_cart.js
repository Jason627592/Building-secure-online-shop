$(document).ready(function() {
    refreshCart();
    $(".shopping_cart").on("click", function() {
        addtocart($(this).attr("id"));
    });

    $("#clearCart").on("click", function() {
        localStorage.removeItem('cart_storage');
        refreshCart();
    });

});

function addtocart(pid) {
    var storage = localStorage.getItem('cart_storage');
    if (storage == undefined)
        storage = {};
    else
        storage = JSON.parse(storage);
    if (storage[pid] == undefined)
        storage[pid] = 0;
    storage[pid]++;
    localStorage.setItem('cart_storage', JSON.stringify(storage));
    refreshCart();
}

function refreshCart() {
    let storage = localStorage.getItem('cart_storage');
    let counter1 = 0;
    let counter2 = 0;
    let cart_html_code = new Array();
    let total = 0;

    if (storage == undefined)
        storage = {};
    else
        storage = JSON.parse(storage);

    for (let item in storage) {
        //console.log(item + " " + storage[item]);
        result = fetchDetails(item);
        counter1++;
    }
    if(counter1==0){
        document.querySelector('#cart_item').innerHTML = "<p>The Shopping Cart is Empty</p>";
    }else{
        document.querySelector('#cart_item').innerHTML = "";
    }
    document.querySelectorAll('.cart_total')[0].innerHTML = 0;
    document.querySelectorAll('.cart_total')[1].innerHTML = 0;

    function fetchDetails(pid) {
        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function() {
            const DONE = 4;
            const OK = 200;
            if (xhr.readyState === DONE && xhr.status === OK) {
                data = JSON.parse(xhr.responseText);
                //console.log(data[0]["name"]);
                counter2++;
                result = '<p><span class="product-name">' + data[0]["name"] + '</span><input type="number" id="'+ pid +'" class="product_quantity" name="quantity" min="0" max="50" value="' + storage[pid] + '">' + '<span class="price">$' + data[0]["price"] + '</span><span class="subtotal">$'+ (storage[pid]*data[0]["price"]).toFixed(1) +'</span></p>';
                total += data[0]["price"]*storage[pid];
                cart_html_code.push(new Array(storage[pid], result));
                if(counter1 <= counter2){
                    document.querySelector('#cart_item').innerHTML = "";
                    document.querySelectorAll('.cart_total')[0].innerHTML = 0;
                    document.querySelectorAll('.cart_total')[1].innerHTML = 0;
                    cart_html_code.sort(function(a, b){return b[0] - a[0]});
                    for (let i=0; i<cart_html_code.length; i++){
                        document.querySelector('#cart_item').innerHTML += cart_html_code[i][1];
                    }
                    document.querySelectorAll('.cart_total')[0].innerHTML = total.toFixed(1);
                    document.querySelectorAll('.cart_total')[1].innerHTML = total.toFixed(1);
                    $(".product_quantity").change(function(){
                        if(this.value<=0){
                            delete storage[this.id];
                        }else{
                            storage[this.id]= this.value;
                        }
                        localStorage.setItem('cart_storage', JSON.stringify(storage));
                        refreshCart();
                    });
                }
            } else if (xhr.status !== 0 && xhr.status !== OK) {
                console.log('Error: ' + xhr.status);
            }
        };

        xhr.open('GET', '../shopping_cart/cart.php?pid=' + pid, true);
        xhr.send();
    }
}

function cartSubmit(){
    var storage = window.localStorage.getItem("cart_storage");
    var shopping_cart_key = Object.keys(JSON.parse(storage));
    var shopping_cart_value = Object.values(JSON.parse(storage));
    if(shopping_cart_key == undefined || shopping_cart_value==undefined){
        return false;
    }

    var form = document.querySelector("#paypal_shopping_cart");
    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function(){
        console.log(xhr);
        if(xhr.readyState == 4 && xhr.status == 200){
            var resp = JSON.parse(xhr.responseText);
            console.log(resp.toString());

            form.custom.value = resp["digest"];
            form.invoice.value = resp["invoice"];
            console.log(resp["total_price"]);
            console.log("result: " + JSON.stringify(resp["test"]));

            for(let x=0; x<shopping_cart_key.length; x++){
                console.table(resp["name_and_price"][x][0]);
                var item_code = x*1+1;
                var newItem = document.createElement("input");
                newItem.type = "hidden";
                newItem.name = "item_name_"+item_code;
                newItem.value = resp["name_and_price"][x][0]["name"].toString(); //Need to search Name

                var newItem2 = document.createElement("input");
                newItem2.type = "hidden";
                newItem2.name = "quantity_"+item_code;
                newItem2.value = shopping_cart_value[x].toString();

                var newItem3 = document.createElement("input");
                newItem3.type = "hidden";
                newItem3.name = "amount_"+item_code;
                newItem3.value = resp["name_and_price"][x][0]["price"].toString(); //Need to search price

                form.appendChild(newItem);
                form.appendChild(newItem2);
                form.appendChild(newItem3);
            }
            console.log(form.invoice.value);

            form.submit();
        }
    }

    xhr.open('POST', '/shopping_cart/cart-process.php?action=genDigest');
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    data = JSON.stringify(shopping_cart_key);
    xhr.send("cart="+data+"&quantity="+JSON.stringify(shopping_cart_value));

    localStorage.removeItem('cart_storage');
    refreshCart();
}
