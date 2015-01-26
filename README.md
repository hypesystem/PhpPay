*Totally cool payments abstraction*

Every time you need to integrate a payments solution, you have to get used to a new library.
That's super annoying.
What if there was a single interface that could be configured to use any of the most popular payments solutions?
This is an attempt at that.
Cool.

To get to know the library, see the [examples](EXAMPLES.md).

There are basically two different ways of handling payments.
This library will strive to eventually support both.
We are starting out with the simplest and most-commonly used (per-project, not per-end-user) one:
the Checkout model.

The Checkout Model
------------------

In the Checkout Model, the user is sent off the product website to complete their payment at a third party.
The thirdt party is hard to get rid off, and using the checkout model saves development time:
no longer is there a need to develop the actual checkout window itself.

![The Checkout Model Illustrated](https://raw.githubusercontent.com/hypesystem/PhpPay/master/TheCheckoutModel.png)

**Known Checkout-Model Payment Methods**

- PayPal Express Checkout
- ePay (Danish)
- NETS (Danish)

There are some alternatives on this general model.
These do not fit directly into the code written for this library:

**Stripe Checkout** overlays the checkout window on your current website, but still requires you to handle the data.

**Gumroad Overlay** is basically a bit of javascript which lets them handle all of the hard work.

The On-Site Model
-----------------

For big business, you will want complete control over the payment.
This model leaves the product website to take care of getting user input.
You should not use this model if you don't use encrypted connections, and your site has to be convincing, or customers won't trust you with their credit card information.

![The On-Site Model Illustrated](https://raw.githubusercontent.com/hypesystem/PhpPay/master/TheOnSiteModel.png)

**Known On-Site Model Payment Methods**

- Stripe

Disclaimer
==========

This library is not just pre-1.0.
It is pre-0.1.
Be very cautions (read the code) before using it in production.

License
=======

Copyright (c) 2015, Niels Roesen Abildgaard <me@hypesystem.dk>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the 'Software'), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
