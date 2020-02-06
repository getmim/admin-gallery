class GalleryForm{

    constructor(){
        this._populateElements()
        this._el.model.classList.add('hidden')
        this._renderModel()
        this._addListener()
    }

    _addListener(){
        
        $(document.body).on('click', '.g-list-item-rm', e => {
            let target = e.target.parentNode.parentNode.parentNode
            $.dialog.confirm('Confirmation', 'Are you sure want to remove this item?', res => {
                if(res)
                    target.remove()
            })
        })

        $(document.body).on('click', '.g-list-item-add', e => {
            let opts = {
                accept   : 'image/*',
                multiple : true,
                form     : 'std-image'
            }

            window['bootstrap-plugins'].Admin.prototype.pickFile(files => {
                files.forEach(file => this._renderItem({url:file.url, label:''}))
            }, opts)
        })

        $(document.body).on('click', '.g-list-thumb', e => {
            window['bootstrap-plugins'].Admin.prototype.viewImage(e.target.href)
            return false
        })

        document.querySelector('#g-form').addEventListener('submit', res => {
            let items = document.querySelectorAll('.g-list-item')
            let result = []

            items.forEach(res => {
                let url   = $(res).find('a').attr('href')
                let label = $(res).find('input').val()
                result.push({url, label})
            })

            this._el.model.value = JSON.stringify(result)
        })
    }

    _hs(text){
        return text
          .replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;")
          .replace(/"/g, "&quot;")
          .replace(/'/g, "&#039;");
    }

    _populateElements(){
        this._el = {
            model: document.querySelector('#admin-gallery-edit-fld-images'),
            list : document.querySelector('#g-list')
        }
    }

    _renderItem(item){
        let safe = {
            url  : this._hs(item.url),
            label: this._hs(item.label)
        }

        let html = `
            <div class="col-md-6 pb-3 g-list-item">
                <div class="media">
                    <a href="${safe.url}" class="mr-3 g-list-thumb" style="background-image:url('${safe.url}')"></a>
                    <div class="media-body">
                        <input type="text" class="form-control form-control-sm mb-1" placeholder="Label" value="${safe.label}">
                        <button class="btn btn-danger btn-sm g-list-item-rm" type="button">Remove</button>
                    </div>
                </div>
            </div>`

        $(this._el.list).append(html)
    }

    _renderModel(){
        let val = this._el.model.value
        if(!val)
            val = '[]'
        
        val = JSON.parse(val)
        if(!val)
            return;

        val.forEach(item => this._renderItem(item))
    }
}

$(() => new GalleryForm)