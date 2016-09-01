<body>
    <div class="page odd  text-center">
        <h1 class="myfixed"><?= $product['description'] ; ?></h1>
    </div>


    <div class="page even">

        <div class="logo">^</div>

        <div class="keybox">
            <div class="logo">
                <?= $product['thumb'] ?>
            </div>

            <div class="gift-header"><?= $product['partcode'] ?> Gift Card</div>

            <div class="key"><?= $product['key'] ?></div>
        </div>



        <div class="float-50l gift-item">
            <span class="message-name">To</span>
            <span class="input"></span>

            <span class="message-name">From</span>
            <span class="input"></span>

            <span class="message-name">Message</span>
            <span class="input box"></span>

        </div>


        <div class="float-50r">
            <div><strong>Redemption Instructions:</strong></div>
        </div>

    </div>

</body>
