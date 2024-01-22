jQuery(document).ready(function ($) {
    // Adiciona suavidade à rolagem horizontal para links com a classe 'scroll-link'
    $(".scroll-link").on("click", function (event) {
        event.preventDefault();
        var target = $(this).attr("href");

        // Verifique se o destino existe
        if ($(target).length) {
            // Obtenha a posição do destino em relação ao contêiner da linha do tempo
            var scrollPosition = $(target).position().left;

            // Rolar suavemente para a posição calculada
            $(".container-timeline").animate({
                scrollLeft: scrollPosition
            }, 800);
        }
    });
});


document.addEventListener("DOMContentLoaded", function () {
    var contentDiv = document.getElementById("myContent");
    var fullscreenBtn = document.getElementById("fullscreenBtn");

    // Adiciona um ouvinte de clique ao botão de tela cheia
    fullscreenBtn.addEventListener("click", function () {
        toggleFullScreen(contentDiv);
    });

    // Função para alternar entre tela cheia e modo normal
    function toggleFullScreen(element) {
        if (!document.fullscreenElement) {
            // Se o elemento não estiver em tela cheia, solicite
            // que ele entre em tela cheia
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.mozRequestFullScreen) {
                element.mozRequestFullScreen();
            } else if (element.webkitRequestFullscreen) {
                element.webkitRequestFullscreen();
            } else if (element.msRequestFullscreen) {
                element.msRequestFullscreen();
            }
        } else {
            // Se o elemento estiver em tela cheia, saia do modo de tela cheia
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }

        // Verifica se está em tela cheia e adiciona ou remove a classe
        checkFullScreen();
    }

    // Função para verificar se está em tela cheia e adicionar/remover a classe
    function checkFullScreen() {
        var isInFullScreen = document.fullscreenElement ||
                            document.mozFullScreenElement ||
                            document.webkitFullscreenElement ||
                            document.msFullscreenElement;

        // Adiciona ou remove a classe com base no status de tela cheia
        if (isInFullScreen) {
            document.querySelector('.botoes-linha').classList.add('position-fixed');
        } else {
            document.querySelector('.botoes-linha').classList.remove('position-fixed');
        }
    }

    // Ouvinte para verificar o status de tela cheia sempre que a tela for alterada
    document.addEventListener("fullscreenchange", checkFullScreen);
    document.addEventListener("mozfullscreenchange", checkFullScreen);
    document.addEventListener("webkitfullscreenchange", checkFullScreen);
    document.addEventListener("msfullscreenchange", checkFullScreen);
});





