import './bootstrap';
import * as bootstrap from 'bootstrap'

window.onload = () => {

    /**
     * 与えられたエレメント ID の要素の onclick リスナを設定する.
     *
     * @param id DOMElement ID
     * @param listener onclick リスナ
     */
    function setOnClickListener(id, listener) {
        const element = document.getElementById(id)
        if (element) {
            element.onclick = listener
        }
    }

    // カレンダーのマス目押下でその日の新規予定作成
    const f = e => e.onclick = () => window.location.href = `/admin/plans/create?date=${e.dataset.date}`
    document.querySelectorAll('table#calendar td, table#calendar td div').forEach(f)

    // タスクのチェック押下
    const g = e => e.onchange = () => e.parentElement.parentElement.submit()
    document.querySelectorAll('.check-task').forEach(g)

    // 公開押下
    setOnClickListener('post-enabled-store', () => {
        document.getElementById('post-enabled').value = true
        const form = document.querySelector('#post form')
        const action = form.getAttribute('action').replace('/create', '').replace('/edit', '')
        form.setAttribute('action', action)
        form.submit()
    })

    // 非公開押下
    setOnClickListener('post-disabled-store', () => {
        document.getElementById('post-enabled').value = false
        const form = document.querySelector('#post form')
        const action = form.getAttribute('action').replace('/create', '').replace('/edit', '')
        form.setAttribute('action', action)
        form.submit()
    })

    // 画像削除
    setOnClickListener('delete-image', () => {
        if (window.confirm('画像を削除します。よろしいですか？')) {
            document.getElementById('delete-image-form').submit()
        }
    })

    // 予定登録
    setOnClickListener('store-plan', () => {
        const form = document.getElementById('plan-form')
        const action = form.getAttribute('action').replace('/create', '').replace('/edit', '')
        form.setAttribute('action', action)
        form.submit()
    })

    // 予定削除
    setOnClickListener('delete-plan', () => {
        if (window.confirm('予定を削除します。よろしいですか？')) {
            document.getElementById('delete-plan-form').submit()
        }
    })

    // タスク編集
    document.querySelectorAll('button.edit-task').forEach(element => {
        const data = element.dataset
        element.onclick = () => {
            document.getElementById('edit-task-form').setAttribute('action', `/admin/tasks/${data.id}`)
            document.getElementById('task-modal-name').value = data.name
            document.getElementById('task-group-id').value = data.groupId
            new bootstrap.Modal(document.getElementById('task-modal')).show()
        }
    })

    // タスク削除
    document.querySelectorAll('.delete-task').forEach(element => {
        element.onclick = () => {
            if (window.confirm('ToDo を削除します。よろしいですか？')) {
                element.parentElement.submit()
            } else {
                return false
            }
        }
    })

    // Wiki 登録
    setOnClickListener('store-wiki', () => {
        const form = document.getElementById('wiki-form')
        const action = form.getAttribute('action').replace('/create', '').replace('/edit', '')
        form.setAttribute('action', action)
        form.submit()
    })

    // Wiki 削除
    setOnClickListener('delete-wiki', () => {
        if (window.confirm('Wiki を削除します。よろしいですか？')) {
            document.getElementById('delete-wiki-form').submit()
        }
    })
}
