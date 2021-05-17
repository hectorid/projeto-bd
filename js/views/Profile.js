import AbstractView from "./AbstractView.js";

export default class Profile extends AbstractView {
    view_html= 'Profile.html'


    constructor(params) {
        super(params);

        this.setTitle('Projeto BD - Perfil')
    }


    async init(container, data) {
        const profile_user_id = this.params?.user_id ?? USER_ID

        let user_profile_data = (await getUserProfileData(profile_user_id))[0]
        console.log(user_profile_data)

        this.setTitle(`Projeto BD - ${user_profile_data.visible_name} (@${user_profile_data.username})`)

        const data_display_elements = {
            visible_name: 'profile-header-visible-name',
            username    : 'profile-header-username',
            following   : 'profile-header-following',
            followers   : 'profile-header-followers',
        }
        const profileHeader = container.querySelector('#profileHeader')
        for(const key in data_display_elements)
            profileHeader.querySelector(`.${data_display_elements[key]}`)
                .innerHTML = user_profile_data[key]

        profileHeader.querySelector('.profile-header-since-date')
            .innerHTML = formatDate(user_profile_data.created_at)

        const profileHeaderButtons = container.querySelector('#profileHeaderButtons')

        profileHeaderButtons.querySelector('.follow-btn')
        if (parseInt(profile_user_id) === USER_ID)
            // Remove the buttons from the document
            profileHeaderButtons.parentNode.removeChild(profileHeaderButtons)
        else {
            profileHeaderButtons.querySelector('.send-message-btn')
                .addEventListener('click', () => {
                    data.navigateTo(`/messages/${profile_user_id}`)
                })


            const follow_btn = profileHeaderButtons.querySelector('.follow-btn')
            // add a "method" to switch the button state
            follow_btn.updateState = (new_state) => {
                follow_btn.setAttribute('data-follow', new_state)

                if (new_state) {
                    follow_btn.classList.remove('btn-primary')
                    follow_btn.classList.add('btn-outline-primary')

                    follow_btn.innerHTML = 'Seguindo'
                }
                else {
                    follow_btn.classList.remove('btn-outline-primary')
                    follow_btn.classList.add('btn-primary')

                    follow_btn.innerHTML = 'Seguir'
                }
            }

            // Check if the current user is following the profile user
            const response = await request(`/api/users/${USER_ID}/follows/${profile_user_id}`)
            follow_btn.updateState(response.data)

            follow_btn.addEventListener('click', on_follow_btn_click)
            async function on_follow_btn_click() {
                const state = follow_btn.getAttribute('data-follow') === 'true'

                const method = (state)
                    ? 'DELETE'
                    : 'POST'

                const response = await request(`/api/users/${USER_ID}/follows/${profile_user_id}`, method)

                showDebugModal(response)

                if (!response.success)
                    return

                follow_btn.updateState(!state)
            }

            // Reveal the buttons
            profileHeaderButtons.classList.remove('visually-hidden')
        }

        if (user_profile_data.profile_picture)
            profileHeader.querySelector('#profileHeaderPicture')
                .setAttribute('src', 'data:image/png;base64,' + user_profile_data.profile_picture)


        const response = await request(`/api/users/${profile_user_id}/posts`)

        const postsContainer = container.querySelector('#postsContainer')

        if (response.data) {
            const postTemplate = container.querySelector('#postTemplate')
            for(const post of response.data) {
                let html = postTemplate.innerHTML
                    .replace(/:USER_ID/g, post.user_id)
                    .replace(/:VISIBLE_NAME/g, post.user_visible_name)
                    .replace(/:USERNAME/g, post.user_username)

                if (post.user_profile_picture)
                    html = html.replace(/src=".*?"/, `src="data:image/png;base64,${post.user_profile_picture}"`)

                postsContainer.append(createElementFromHTML(html))
            }
        }

    }
}