$("#add-image").click(function () {
    // récupérer le N° des futurs champs
  
    const index = +$("#widget-count").val();
  
    // Récupérer le prototype des entrées
  
    const tmpl = $("#annonce_images")
      .data("prototype")
      .replace(/__name__/g, index);
  
    // injecter le code dans la div
  
    $("#annonce_images").append(tmpl);
  
    // on ajoute 1 à la valeur initiale de la collection
  
    $("#widget-count").val(index + 1);
  
    deleteButtons();
  });
  
  function updateCounter() {
    const count = +$("#annonce_images div.form-group").length;
    // on met à jour la valeur de widget-counter
  
    $("#widget-count").val(count);
  }
  
  function deleteButtons() {
    $('button[data-action = "delete"]').click(function () {
      const target = this.dataset.target;
      $(target).remove();
    });
  }
  
  updateCounter();
  deleteButtons();
  