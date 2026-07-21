document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll(".dandy-share-wrap").forEach(wrapper => {

        const button = wrapper.querySelector(".dandy-share");
        const menu = wrapper.querySelector(".dandy-share-menu");

        const title = button.dataset.title;
        const text = button.dataset.text;
        const url = button.dataset.url;


        // Build fallback URLs

        wrapper.querySelector(".dandy-share-facebook").href =
            `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;

        wrapper.querySelector(".dandy-share-x").href =
            `https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}`;

        wrapper.querySelector(".dandy-share-bluesky").href =
            `https://bsky.app/intent/compose?text=${encodeURIComponent(title + " " + url)}`;

        wrapper.querySelector(".dandy-share-email").href =
            `mailto:?subject=${encodeURIComponent(title)}&body=${encodeURIComponent(url)}`;


        button.addEventListener("click", async () => {


            if (navigator.share) {

                try {

                    await navigator.share({
                        title: title,
                        text: text,
                        url: url
                    });

                } catch (e) {

                    // User cancelled

                }

                return;

            }


            // fallback menu

            const isOpen = !menu.hidden;

            menu.hidden = isOpen;

            button.setAttribute(
                "aria-expanded",
                String(!isOpen)
            );

        });


        const copyButton = wrapper.querySelector(".dandy-share-copy");

        copyButton.addEventListener("click", async () => {

            try {

                await navigator.clipboard.writeText(url);

                copyButton.textContent = "Copied!";

                setTimeout(() => {
                    copyButton.textContent = "Copy link";
                }, 1500);

            } catch (e) {

                window.prompt(
                    "Copy this link:",
                    url
                );

            }

        });


    });

});