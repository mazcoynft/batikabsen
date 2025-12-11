@vite('resources/css/login.css')
<div id="slogan">
    <h3>Solusi Digitalisasi </h3>
    <h4>Lembaga Keuangan Anda!</h4>
</div>

<style>
body {
    background: rgb(12, 141, 240) !important;
    background: linear-gradient(0deg, rgb(16, 203, 206) 0%, rgba(253,187,45,1) 100%)  !important;;
}

@media screen and (min-width: 1024px) {
    main {
        position: absolute; right: 100px;
    }

    main:before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: darkcyan;
        border-radius: 12px;
        z-index: -9;

        /*box-shadow: -50px 80px 4px 10px #555;*/
        -webkit-transform: rotate(7deg);
        -moz-transform: rotate(7deg);
        -o-transform: rotate(7deg);
        -ms-transform: rotate(7deg);
        transform: rotate(7deg);
    }
    .fi-logo {
        position: fixed;
        left: 100px;
        font-size: 3em;
        color: cornsilk;
    }

    #slogan {
        position: fixed;
        left: 100px;
        margin-top: 50px;
        color: bisque;
        font-family: cursive;
        font-size: 2em;
        font-weight: bold;
        text-shadow: #3f6212 2px 2px 5px;
    }
}

</style>