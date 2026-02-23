document.querySelectorAll(".btn-hero").forEach(btn=>{
    btn.addEventListener("mouseover",()=>{
        btn.style.boxShadow="0 5px 15px rgba(0,0,0,0.2)";
    });

    btn.addEventListener("mouseout",()=>{
        btn.style.boxShadow="none";
    });
});