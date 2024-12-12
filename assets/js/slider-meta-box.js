document.addEventListener("DOMContentLoaded", function () {
    const slidersContainer = document.getElementById("sliders-container");
    const addSliderButton = document.getElementById("add-slider");

    let sliderCount = slidersContainer.children.length || 0;
    let mediaFrame = null; // Single media frame for all image uploads

    // Function to create a new slider
    function createSliderConfig(sliderId) {
        const sliderDiv = document.createElement("div");
        sliderDiv.classList.add("sp-slider-config");

        sliderDiv.innerHTML = `
            <label>Slider ID:</label>
            <input type="text" name="sp_sliders[${sliderId}][id]" value="${sliderId}" readonly />

            <label>Slider Type:</label>
            <select name="sp_sliders[${sliderId}][type]">
                <option value="text_icon">Text + Icon</option>
                <option value="stacked">Stacked</option>
            </select>

            <div class="slides-repeater">
                <h4>Slides</h4>
                <button type="button" class="button add-slide">Add Slide</button>
                <div class="slides-container"></div>
            </div>

            <button type="button" class="button button-secondary remove-slider">Remove Slider</button>
        `;

        return sliderDiv;
    }

    // Function to create a new slide
    function createSlideConfig(sliderId, slideIndex) {
        const slideDiv = document.createElement("div");
        slideDiv.classList.add("slide-item");

        slideDiv.innerHTML = `
            <label>Slide Image:</label>
            <input type="hidden" name="sp_sliders[${sliderId}][slides][${slideIndex}][image]" />
            <button type="button" class="button upload-image">Upload Image</button>
            <img src="" class="preview-image" style="max-width: 100px; display: none;" />

            <label>Primary Text Block:</label>
            <input type="text" name="sp_sliders[${sliderId}][slides][${slideIndex}][primary_text][content]" placeholder="Enter text" />
            <label>Text Type:</label>
            <select name="sp_sliders[${sliderId}][slides][${slideIndex}][primary_text][type]">
                <option value="p">Paragraph</option>
                <option value="h1">Heading 1</option>
                <option value="h2">Heading 2</option>
                <option value="h3">Heading 3</option>
                <option value="h4">Heading 4</option>
                <option value="h5">Heading 5</option>
                <option value="h6">Heading 6</option>
            </select>

            <label>Secondary Text Block (Optional):</label>
            <input type="text" name="sp_sliders[${sliderId}][slides][${slideIndex}][secondary_text][content]" placeholder="Enter text" />
            <label>Text Type:</label>
            <select name="sp_sliders[${sliderId}][slides][${slideIndex}][secondary_text][type]">
                <option value="">None</option>
                <option value="p">Paragraph</option>
                <option value="h1">Heading 1</option>
                <option value="h2">Heading 2</option>
                <option value="h3">Heading 3</option>
                <option value="h4">Heading 4</option>
                <option value="h5">Heading 5</option>
                <option value="h6">Heading 6</option>
            </select>

            <button type="button" class="button button-secondary remove-slide">Remove Slide</button>
        `;

        return slideDiv;
    }

    // Delegate events for dynamically added elements
    // Delegate events for dynamically added elements
    slidersContainer.addEventListener("click", function (event) {
        const target = event.target;

        // Add Slide button
        if (target.classList.contains("add-slide")) {
            const slidesContainer = target.closest(".sp-slider-config").querySelector(".slides-container");
            const sliderId = target.closest(".sp-slider-config").querySelector("input[type='text']").value;
            const slideIndex = slidesContainer.children.length;
            const newSlide = createSlideConfig(sliderId, slideIndex);
            slidesContainer.appendChild(newSlide);
        }

        // Remove Slide button
        if (target.classList.contains("remove-slide")) {
            target.closest(".slide-item").remove();
        }

        // Upload Image button
        if (target.classList.contains("upload-image")) {
            const slideDiv = target.closest(".slide-item");
            const fileInput = slideDiv.querySelector("input[type='hidden']");
            const previewImage = slideDiv.querySelector(".preview-image");

            // Create the media frame if it doesn't already exist
            if (!mediaFrame || mediaFrame.el) {
                mediaFrame = wp.media({
                    title: "Select Image",
                    button: { text: "Use This Image" },
                    multiple: false,
                });

                mediaFrame.on("select", function () {
                    const attachment = mediaFrame.state().get("selection").first().toJSON();
                    fileInput.value = attachment.url; // Set the image URL in the hidden field
                    previewImage.src = attachment.url; // Show a preview of the selected image
                    previewImage.style.display = "block";
                });
            }

            mediaFrame.open(); // Open the media frame
        }

        // Remove Slider button
        if (target.classList.contains("remove-slider")) {
            target.closest(".sp-slider-config").remove();
        }
    });


    // Add slider button functionality
    addSliderButton.addEventListener("click", function () {
        sliderCount++;
        const sliderId = `slider_${sliderCount}`;
        const newSlider = createSliderConfig(sliderId);
        slidersContainer.appendChild(newSlider);
    });
});
