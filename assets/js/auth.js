/* Lightweight auth behaviors: show/hide password and simple form checks */
document.addEventListener('DOMContentLoaded', function(){
  // Toggle password visibility for any .toggle-password element
  document.querySelectorAll('.toggle-password').forEach(function(btn){
    btn.addEventListener('click', function(e){
      var target = document.querySelector(this.getAttribute('data-target'));
      if(!target) return;
      if(target.type === 'password'){
        target.type = 'text';
        this.textContent = 'Hide';
      } else {
        target.type = 'password';
        this.textContent = 'Show';
      }
    });
  });

  // Simple client-side validation before submit (progressive enhancement)
  document.querySelectorAll('form[data-auth="true"]').forEach(function(form){
    form.addEventListener('submit', function(ev){
      var valid = true;
      var email = this.querySelector('input[type="email"]');
      var pass = this.querySelector('input[type="password"]');
      if(email){
        var re = /\S+@\S+\.\S+/;
        if(!re.test(email.value)) { valid = false; email.classList.add('is-invalid'); }
        else { email.classList.remove('is-invalid'); }
      }
      if(pass){
        if(pass.value.length < 6) { valid = false; pass.classList.add('is-invalid'); }
        else { pass.classList.remove('is-invalid'); }
      }
      if(!valid){ ev.preventDefault(); }
    });
  });
});

/* end auth.js */
