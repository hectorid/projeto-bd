import AbstractView from "./AbstractView.js";

export default class Settings extends AbstractView {
    view_html= 'Settings.html'


    constructor(params) {
        super(params);

        this.setTitle('Projeto BD - Configurações')
    }


    async init(container) {
        const formEditUser = container.querySelector('#formEditUser')
        prepare_form(formEditUser)

        // Fill the input fields with the user data
        const user_data = await getUserData()
        const pre_filled_fields = ['visible_name', 'username', 'birthdate', 'email']
        for(const name of pre_filled_fields)
            formEditUser.querySelector(`[name=${name}]`).value = user_data[name]

        // Attach the submit callback
        formEditUser.addEventListener('submit', on_formEditUser_submit, true)
        async function on_formEditUser_submit(event) {
            event.preventDefault()

            let form_data_obj = convertFormDataToObject(formEditUser)

            try {
                for (const input in form_data_obj) {
                    form_data_obj[input] = form_data_obj[input].trim()
                    // The password field is not required
                    if (input !== 'password' && form_data_obj[input] === '')
                        throw 'Preencha todos os campos'
                }

                const new_password = form_data_obj['password']
                const password_confirmation = formEditUser.querySelector('#formEditUserPasswordConfirmation').value
                // Check if the password has been confirmed
                if (new_password || password_confirmation && (new_password !== password_confirmation))
                    throw 'As senhas não são iguais'

                const picture_file = formEditUser.querySelector('#formEditUserPictureFile').files[0]
                if (picture_file) {
                    const response = await fileToBase64(picture_file)

                    if (response instanceof Error)
                        throw Error

                    form_data_obj.profile_picture = response
                }
            } catch (error_msg) {
                formEditUser.alert.show(error_msg)
                return
            }


            // Remove data that has not been changed
            for (const input in form_data_obj) {
                if (form_data_obj[input] === user_data[input])
                    delete form_data_obj[input]
            }
            if (form_data_obj.password === '')
                delete form_data_obj.password

            if (isEmptyObject(form_data_obj)) {
                formEditUser.alert.show('Não houve alteração nos dados', 'warning')
                return
            }

            formEditUser.fieldset.disabled = true
            formEditUser.alert.hide()

            const response = await request(`/api/users/${USER_ID}`, 'PUT', form_data_obj)

            showDebugModal(response)

            if (!response.success)
                formEditUser.alert.show('Ocorreu um erro ao processar a requisição')
            else
                formEditUser.alert.show('Dados atualizados', 'success')

            formEditUser.fieldset.disabled = false
        }

        // Enable the form
        formEditUser.fieldset.disabled = false


        // Attach the user deletion request to the button
        const btnDeleteUser = container.querySelector('#btnDeleteUser')
        btnDeleteUser.addEventListener('click', on_btnDeleteUser_click)
        async function on_btnDeleteUser_click() {
            const response = await request(`/api/users/${USER_ID}`, 'DELETE')

            showDebugModal(response)

            if (!response.success)
                return

            // Log the user out
            window.location = '/auth/logout.php';
        }
    }
}