$(document).ready(function () {
    $(".select2").select2();
});

// async
const data = {
    src: async (query) => {
      try {
        // Fetch Data from external Source
        const source = await fetch(BASEURL + 'settings/allProducts');
        const data = await source.json();
        return data;
      } catch (error) {
        return error;
      }
    },
    keys: ["product_id", "product"],
    cache: true
}

if($("#product_holder").length) {

    var autoCompletePoNum = new autoComplete({
        selector: "#product_holder",
        placeHolder: "Search for Product code or Name...",
        data: data,
        threshold: 4,
        resultsList: {
            element: function element(list, data) {
                if (!data.results.length) {
                    // Create "No Results" message element
                    var message = document.createElement("div");
                    // Add class to the created element
                    message.setAttribute("class", "no_result");
                    // Add message text content
                    message.innerHTML = "<span>Found No Results for \"" + data.query + "\"</span>";
                    // Append message element to the results list
                    list.prepend(message);
                }
            },
            noResults: true
        },
        resultItem: {
            highlight: true
        },
        events: {
            input: {
                selection: function selection(event) {
                    //console.log(event.detail.selection.value);
                    var selection = event.detail.selection.value;
                    $('#product_id').val(selection.product_id);
                    autoCompletePoNum.input.value = selection.product;
                }
            }
        }
    });
}
