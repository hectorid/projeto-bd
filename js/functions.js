function createElementFromHTML(html) {
    const div = document.createElement('div')
    div.innerHTML = html.trim()

    return div.firstElementChild
}


function convertFormDataToObject(form_element) {
    let form_data = new FormData(form_element)

    console.log('form data: ', new Map(form_data.entries()))

    // Create an Object from an array of pairs [key, value]
    return Object.fromEntries(
        // Create an Array of keys from the .keys() iterator
        Array.from(form_data.keys())
            // Maps each key to a pair [key, value(s)]
            .map(key => [
                key,
                // If there is more than 1 value for the key:
                form_data.getAll(key).length > 1
                    ? form_data.getAll(key) //   - get an array of values;
                    : form_data.get(key)    //   - get the single value.
            ])
    )
}


function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader()
        reader.readAsDataURL(file)
        reader.onload = () => resolve(reader.result)
        reader.onerror = (error) => reject(error)
    })
}


function formatDate(date) {
    return (new Date(date)).toLocaleDateString('pt-BR')
}
function formatDateTime(date) {
    return (new Date(date)).toLocaleString('pt-BR')
}


function getParamsFromURI(path, uri) {
    return pathToRegex(path).exec(uri).groups
}


function isEmptyObject(value) {
    // Use this for older browsers
    // return (
    //     Object.prototype.toString.call(value) === '[object Object]' &&
    //     JSON.stringify(value) === '{}'
    // )
    return (
        value &&
        Object.keys(value).length === 0 &&
        value.constructor === Object
    )
}


// Creates a RegEx pattern based on the path
function pathToRegex(path) {
    return new RegExp(
        // Match the start of the string
        '^'
        + path
            // Escape the bars '/'
            .replace(/\//g, "\\/")
            // Replace the ":params" with a capture group that match anything until the next '/' or '?'
            .replace( /:([^\\\/?]+)/g, "(?<$1>[^\\\/?]+)")
        // Match the end of the string
        + '$', 'g'
    )
}


function prepare_form(form) {
    // Save a reference of the fieldset
    form.fieldset = form.getElementsByTagName('fieldset')[0]
    // Save a reference of the alert and some helper functions
    form.alert = form.getElementsByClassName('alert')[0]
    form.alert.show = (message, type = 'danger') => {
        // Change the alert type (by replacing the alert-____ class)
        const types = ['danger', 'warning', 'success']
        for(const t of types)
            form.alert.classList.remove(`alert-${t}`)
        form.alert.classList.add(`alert-${type}`)
        // Insert the message
        form.alert.innerHTML = message
        // Show the alert
        form.alert.classList.remove('visually-hidden')
    }
    form.alert.hide = () => {
        form.alert.classList.add('visually-hidden')
    }
}


// TODO: create a form validation function


async function request(url, method = 'GET', body = {}, response_type = 'json') {
    try {
        let init = {
            method: method,
            headers: {'Content-Type' : 'aplication/json'}
        }
        if (method !== 'GET')
            init.body = JSON.stringify(body)

        const fetch_response = await fetch(url, init)

        if (!fetch_response.ok)
            return false

        let response
        if (response_type === 'json')
            response = await fetch_response.json()
        else
            response = await fetch_response.text()

        console.log('request', url, method, body, 'response:', response)

        return response
    } catch (error) {
        console.error(error)
        return false
    }
}


// TODO: disable it before presentation
const debugModalElement = document.getElementById('debugModal')
const debugModal = new  bootstrap.Modal(debugModalElement)
function showDebugModal(response) {
    if (response?.debug_output === undefined) return

    debugModalElement
        .getElementsByClassName('modal-body')[0]
        .innerHTML = response.debug_output
    // debugModal.show()
}


async function getUserData(user_id = USER_ID) {
    const response = await request(`/api/users/${user_id}`)

    if (!response || !response?.success)
        console.error('Error on the user data request')

    return response.data[0]
}
async function getUserProfileData(user_id = 0) {
    const request_uri = (user_id > 0)
        ? `/api/users/${user_id}/profile`
        : '/api/users/profiles'

    const response = await request(request_uri)

    showDebugModal(response)

    if (!response || !response?.success)
        console.error('Error on the user profile data request')

    return response.data
}
