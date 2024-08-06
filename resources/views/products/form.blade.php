<form action="{{ route('createProduct') }}" method="post" enctype="multipart/form-data">
    @csrf
    title<input type="text" name="title"><br>
    product_name<input type="text" name="product_name"><br>
    description<input type="text" name="description"><br>
    category_id<input type="text" name="category_id"><br>
    height<input type="text" name="height"><br>
    length<input type="text" name="length"><br>
    width<input type="text" name="width"><br>
    package_weight_value<input type="text" name="package_weight_value"><br>
    package_weight_unit<input type="text" name="package_weight_unit"><br>
    package_dimension_unit<input type="text" name="package_dimension_unit"><br>
    images<input type="file" name="images"><br>
    skus_price_amount<input type="text" name="skus_price_amount"><br>
    skus_price_currency<input type="text" name="skus_price_currency"><br>
    skus_inventory<input type="text" name="skus_inventory"><br>
    skus_quantity<input type="text" name="skus_quantity"><br>
    <input type="submit" name="submit-form">
</form>
