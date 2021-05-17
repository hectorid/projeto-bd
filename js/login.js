// IIFE (Immediately Invoked Function Expression)
(function() {
    // Setup the Sign In form
    const formSignIn = document.getElementById('formSignIn')
    prepare_form(formSignIn)

    formSignIn.addEventListener('submit', on_formSignIn_submit, true)
    async function on_formSignIn_submit(event) {
        event.preventDefault() // Stop the form submission

        let form_data_obj = convertFormDataToObject(formSignIn)

        try {
            for (const input in form_data_obj) {
                form_data_obj[input] = form_data_obj[input].trim()
                if (form_data_obj[input] === '')
                    throw 'Preencha todos os campos'
            }
        } catch (error_msg) {
            formSignIn.alert.show(error_msg)
            return
        }

        formSignIn.fieldset.disabled = true
        formSignIn.alert.hide()

        const response = await request('/auth/login.php', 'POST', form_data_obj)

        showDebugModal(response)

        if (!response.success) {
            formSignIn.alert.show('Informações de login incorretas!')
            formSignIn.fieldset.disabled = false
            return
        }

        // TODO: if a not-logged in user tries to access an restricted page, redirect him after the login is successful
        // Redirect to the home page
        window.location = '/home';
    }

    document.getElementById('btnSignUp')
        .addEventListener('click', showSignUp)
    function showSignUp() {
        formSignIn.classList.add('visually-hidden')
        formSignIn.fieldset.disabled = true

        formSignUp.classList.remove('visually-hidden')
        formSignUp.fieldset.disabled = false
    }


    // Setup the Sign Up form
    const formSignUp = document.getElementById('formSignUp')

    prepare_form(formSignUp)

    formSignUp.addEventListener('submit', on_formSignUp_submit, true)
    async function on_formSignUp_submit(event) {
        event.preventDefault() // Stop the form submission

        let form_data_obj = convertFormDataToObject(formSignUp)

        try {
            for (const input in form_data_obj) {
                form_data_obj[input] = form_data_obj[input].trim()
                if (form_data_obj[input] === '')
                    throw 'Preencha todos os campos'
            }

            // Check if the password has been confirmed
            const password_confirmation = formSignUp.querySelector('#formSignUpPasswordConfirmation').value
            if (form_data_obj['password'] !== password_confirmation)
                throw 'As senhas não são iguais'
        } catch (error_msg) {
            formSignUp.alert.show(error_msg)
            return
        }

        formSignUp.fieldset.disabled = true
        formSignUp.alert.hide()

        const response = await request('/api/users', 'POST', form_data_obj)

        showDebugModal(response)

        if (!response.success) {
            formSignIn.alert.show('Informações de login incorretas!')
            formSignIn.fieldset.disabled = false
            return
        }

        formSignIn.alert.show('Usuário criado com sucesso! Agora é só fazer o login :)', 'success')
        showSignIn()
    }

    document.getElementById('btnSignIn')
        .addEventListener('click', showSignIn)
    function showSignIn() {
        formSignUp.classList.add('visually-hidden')
        formSignUp.fieldset.disabled = true

        formSignIn.classList.remove('visually-hidden')
        formSignIn.fieldset.disabled = false
    }


    // Enable the sign in form
    formSignIn.fieldset.disabled = false
})();