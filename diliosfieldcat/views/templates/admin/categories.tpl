{literal}
    <script>
    window.addEventListener("DOMContentLoaded", (event) => {
        let img = document.querySelector('#category_dfc_image');
        
        /**
        * @param {HTMLElement} element
        * @param string img_url
        * @param string button_url
        */
        const addImage = (element, img_url, button_url) => {
            let div =  document.createElement('div')
            let figure =  document.createElement('figure')
            let image =  document.createElement('img')
            let figCaption =  document.createElement('figcaption')
            let deleteBtn =  document.createElement('button')

            figure.classList.add("figure")
            figCaption.classList.add("figure-caption")
            image.classList.add("figure-img")
            image.classList.add("img-thumbnail")
            image.classList.add("img-fluid")
            deleteBtn.classList.add('btn')  
            deleteBtn.classList.add("btn-outline-danger")  
            deleteBtn.classList.add("btn-sm")  
            deleteBtn.classList.add("js-form-submit-btn")  

            image.src = img_url
            deleteBtn.setAttribute('data-form-submit-url', button_url)
            deleteBtn.setAttribute('data-form-csrf-token', button_url) 

            deleteBtn.innerHTML ='<i class="material-icons">delete_forever</i>'

            div.appendChild(figure)
            figure.appendChild(image)
            figure.appendChild(figCaption)
            figCaption.appendChild(deleteBtn)
            element.appendChild(div)
        }
        if(img) {
            let issetImage = img.dataset.issetImage 
            let image = img.dataset.image
            let deleteLink = img.dataset.deleteLink
            if(issetImage == 1)
                addImage(img.parentElement.parentElement, image, deleteLink)
        }
    });
</script>
{/literal}
