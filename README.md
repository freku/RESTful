## Products Catalog API:

    * POST /api/product [Creates new product]
        With needed fields 'title' and 'price'. 
            Example: {"title": "Cake", "price": "4.99 USD"}
        Results with:
            {"code":200,"id":11,"title":"Cake","price":"4.99 USD","cart":null}

    * GET /api/product/{id} [Get a product data]
        Where 'id' is id of product.
        Results with:
            {"code":200,"id":11,"title":"Cake","price":"4.99 USD","cart":null}
            
    * PUT /api/product/{id} [Update a product]
        With fields 'title' and/or 'price'.
            {"title": "New Title"}
        Results with:
            {"code":200,"id":11,"title":"New Title","price":"4.99 USD","cart":null}

    * DELETE /api/product/{id} [Delete a product]
        Results with:
            {"code":200,"id":null,"title":"New Title","price":"4.99 USD","cart":null}

    * GET /api/product/list/{page} [Get page of maxiumum three products]
        Where 'page' is the number of page you want to get.
        Results with:
            {"code":200,"products":[ArrayOfProducts]}


## Cart API:

    * POST /api/cart [Create a cart]
        Results with:
            {"code":200,"id":1}
    
    * PUT /api/cart/{cartId}/add-product/{productId} [Add a product to a cart]
        Both ids are integers.
        Results with:
            {"code":200,"message":"Product 1 added successfully to 1 cart."}
    
    * PUT /api/cart/{cartId}/remove-product/{productId} [Remove a product from a cart]
        Results with:
            {"code":200,"message":"Product 1 removed successfully from 1 cart."}

    * GET /api/cart/{id}/list [Get list of all products in a cart]
        Where 'id' is cart's ID
        Results with (when no products were added):
            {"code":200,"products":[],"total_price":"0 USD"}

## To run tests:
```
php bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:schema:create

php bin/console --env=test doctrine:fixtures:load --purge-with-truncate
php bin/console --env=test doctrine:fixtures:load
php ./vendor/bin/phpunit tests/Controller
```

If you run the application on other host than localhost:8000
you will have to change HTTP_HOST parameter in setUp() method
in CartControllerTest.php and ProductControllerTest.php to run
tests successfully 😉
