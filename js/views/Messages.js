import AbstractView from "./AbstractView.js";

export default class Messages extends AbstractView {
    view_html= 'Messages.html'


    constructor(params) {
        super(params);

        this.setTitle('Projeto BD - Mensagens')
    }


    async init(container) {
        let current_chat_user_id = this.params?.user_id ?? 0


        const chatList = container.querySelector('#chatList')
        const chatListItemTemplate = container.querySelector('#chatListItemTemplate')

        async function loadChats() {
            const chats_response = await request(`/api/users/${USER_ID}/chats`)

            if (!chats_response.data) {
                chatList.innerHTML = "<div class='p-3'>Você ainda não abriu nenhum chat :(</div>"
                return
            }

            chatList.innerHTML = '';
            for (const chat of chats_response.data) {
                let html = chatListItemTemplate.innerHTML
                    .replace(/:CHAT_USER_ID/, chat.chat_user_id)
                    .replace(/:VISIBLE_NAME/g, chat.chat_visible_name)

                if(chat.chat_profile_picture)
                    html = html.replace(/src=".*?"/, `src="data:image/png;base64,${chat.chat_profile_picture}"`)

                const chat_list_element = createElementFromHTML(html)
                chat_list_element.addEventListener('click', () => {

                    const chat_list_item_id = chat_list_element.getAttribute('data-id')
                    loadMessages(chat_list_item_id)
                })

                chatList.append(chat_list_element)
            }
        }
        await loadChats()


        const chatHeaderLink = container.querySelector('#chatHeaderLink')

        const messageBoardButtons = container.querySelector('#messageBoardButtons')

        const messageEditCollapseElement = container.querySelector('#messageEditCollapse')
        const messageEditCollapse = bootstrap.Collapse.getInstance(messageEditCollapseElement)
        console.log(messageEditCollapseElement, messageEditCollapse)
        // const messageEditCollapse = new bootstrap.Collapse(messageEditCollsapeElement)

        const messageBoard = container.querySelector('#messageBoard')
        messageBoard.selected_message = null
        messageBoard.addEventListener('click', (event) => {
            if (event.target.matches('.message-sent')) {
                const message_element = event.target
                message_element.setSelected(true)

                if (messageBoard.selected_message !== message_element) {
                    messageBoard.selected_message?.setSelected(false)
                }
                messageBoard.selected_message = message_element

                messageBoardButtons.disabled = false
            }
            else {
                messageBoardButtons.disabled = true
                messageBoard.selected_message?.setSelected(false)
                messageEditCollapse?.hide()
            }
        })

        const messageReceivedTemplate = container.querySelector('#messageReceivedTemplate')
        const messageSentTemplate = container.querySelector('#messageSentTemplate')
        function buildMessageElement(template, data) {
            // TODO: build info string
            let info = ''

            let tooltip_text = `Enviada em ${formatDateTime(data.created_at)}`
            if(data.last_edited_at)
                tooltip_text += `\nEditada em ${formatDateTime(data.last_edited_at)}`

            let html = template.innerHTML
                .replace(/:ID/, data.id)
                .replace(/:TEXT/, data.text)
                .replace(/:INFO/, info)
                .replace(/:CREATED_AT/, data.created_at)
                .replace(/:LAST_EDITED_AT/, data.last_edited_at)
                .replace(/:TOOLTIP_TEXT/, tooltip_text)

            const element = createElementFromHTML(html)
            element.setSelected = (selected) =>  {
                if ((element.getAttribute('data-selected') === 'true') ===  selected)
                    return

                element.setAttribute('data-selected', selected)

                if (selected) {
                    element.classList.remove('bg-secondary')
                    element.classList.add('bg-primary')
                }
                else {
                    element.classList.remove('bg-primary')
                    element.classList.add('bg-secondary')
                }
            }

            return element
        }

        async function loadMessages(chat_user_id) {
            // Add a new entry to the browser's history
            history.pushState({}, '', `/messages/${chat_user_id}`)

            current_chat_user_id = chat_user_id

            chatList.querySelector('.active')?.classList.remove('active')
            chatList.querySelector(`[data-id="${chat_user_id}"]`)?.classList.add('active')

            let user_data = await getUserData(chat_user_id)

            chatHeaderLink.setAttribute('href', `/profile/${chat_user_id}`)
            chatHeaderLink.querySelector('.chat-header-visible-name').innerHTML = user_data.visible_name
            chatHeaderLink.querySelector('.chat-header-username').innerHTML = user_data.username

            const response = await request(`/api/users/${USER_ID}/messages/${chat_user_id}`)

            messageBoard.innerHTML = ''
            for (const message of response.data) {
                let template = (message.sent_by === USER_ID)
                    ? messageSentTemplate
                    : messageReceivedTemplate

                const message_element = buildMessageElement(template, message)

                messageBoard.append(message_element)
            }
        }


        if(!(chatList.querySelector(`[data-id="${current_chat_user_id}"]`)) && chatList.firstChild)
            current_chat_user_id = chatList?.firstChild.getAttribute('data-id')

        if (current_chat_user_id > 0)
            loadMessages(current_chat_user_id).then()


        const formSendMessage = container.querySelector('#formSendMessage')
        formSendMessage.addEventListener('submit', on_formSendMessage_submit)
        async function on_formSendMessage_submit(event) {
            event.preventDefault()

            let form_data_obj = convertFormDataToObject(formSendMessage)

            if (form_data_obj.text === '')
                return

            formSendMessage.querySelector('textarea').value = ''

            if (messageBoard.selected_message) {
                const old_message_element = messageBoard.selected_message
                const message_id = old_message_element.getAttribute('data-id')

                messageBoard.selected_message = null

                const response = await request(`/api/messages/${message_id}`, 'PUT', form_data_obj)

                const new_message_data = {
                    id: message_id,
                    text: form_data_obj.text,
                    created_at: old_message_element.getAttribute('data-created-at'),
                    last_edited_at: response.data.last_edited_at
                }

                const new_message_element = buildMessageElement(messageSentTemplate, new_message_data)

                old_message_element.replaceWith(new_message_element)
            }
            else {
                const response = await request(`/api/users/${USER_ID}/messages/${current_chat_user_id}`, 'POST', form_data_obj)
                const new_message_data = {
                    id: response.data.id,
                    text: form_data_obj.text,
                    created_at: response.data.created_at
                }

                const new_message_element = buildMessageElement(messageSentTemplate, new_message_data)
                messageBoard.prepend(new_message_element)
            }
        }

        // Attach the user deletion request to the button
        const btnDeleteMessage = container.querySelector('#btnDeleteMessage')
        btnDeleteMessage.addEventListener('click', on_btnDeleteMEssage_click)
        async function on_btnDeleteMEssage_click() {
            const selected_message_id = messageBoard.selected_message.getAttribute('data-id')

            const response = await request(`/api/messages/${selected_message_id}`, 'DELETE')

            showDebugModal(response)

            messageBoard.removeChild(messageBoard.selected_message)
            messageBoard.selected_message = null
        }
    }
}