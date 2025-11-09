document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("opinionForm");
  const success = document.getElementById("successMessage");
  const postList = document.getElementById("postList");

  // Charger les posts sauvegardés
  const loadPosts = () => {
    postList.innerHTML = "";
    const posts = JSON.parse(localStorage.getItem("opinions") || "[]");

    if (posts.length === 0) {
      postList.innerHTML = "<p>Aucun avis publié pour le moment.</p>";
      return;
    }

    posts.reverse().forEach(p => {
      const card = document.createElement("div");
      card.className = "post-card";
      card.innerHTML = `
        <h2>${p.title}</h2>
        <p><strong>${p.author}</strong> — 📍 ${p.location}</p>
        <p>${p.opinion}</p>
        <small>${p.date}</small>
      `;
      postList.appendChild(card);
    });
  };

  loadPosts(); // Affiche les posts existants

  // Validation + enregistrement
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    let valid = true;

    // Nettoyage des erreurs
    form.querySelectorAll(".error").forEach(el => el.remove());
    form.querySelectorAll("input, textarea").forEach(input => {
      input.classList.remove("input-error");

      if (!input.checkValidity()) {
        valid = false;
        const error = document.createElement("div");
        error.className = "error";
        error.textContent = input.validationMessage;
        input.insertAdjacentElement("afterend", error);
        input.classList.add("input-error");
      }
    });

    if (valid) {
      const post = {
        title: form.title.value.trim(),
        author: form.author.value.trim(),
        location: form.location.value.trim(),
        opinion: form.opinion.value.trim(),
        date: new Date().toLocaleString("fr-FR")
      };

      const posts = JSON.parse(localStorage.getItem("opinions") || "[]");
      posts.push(post);
      localStorage.setItem("opinions", JSON.stringify(posts));

      form.reset();
      success.style.display = "block";
      loadPosts();
      window.scrollTo({ top: success.offsetTop - 100, behavior: "smooth" });
    }
  });
});
