<x-simple-modal name="add-tag" x-data="addTag">
    <x-slot name="title">
        Add Tag
    </x-slot>

    <x-slot name="content">
        <div class="flex flex-col">
            <label for="name" class="">Name</label>
            <input type="text" class="text-black form-control" id="input" placeholder="Name..."/>
        </div>
        <span class="text-red-500" x-text="error"></span>

        <div>
            <label for="personal" class="">Personal?</label>
            <input type="checkbox" id="personal" class="text-black form-control"/>
        </div>
    </x-slot>

    <x-slot name="buttons">
        <button class="p-1 mt-4 bg-green-600 border rounded btn dark:bg-green-700 hover:bg-gray-400 hover:dark:bg-gray-500" type="button" x-on:click="validate">Add</button>
        <button class="p-1 mt-4 bg-red-700 border rounded btn dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" type="button" x-on:click="closeModal">Cancel</button>
    </x-slot>
</x-simple-modal>

<script>
    function addTag() {
        const input = document.getElementById('input');
        const personal = document.getElementById('personal');

        return {
            error: '',

            eventName()
            {

                if(this.context != null && this.context.event != null)
                {
                    return this.context.event;
                }
                return 'Selected'
            },

            validate() {
                const value = input.value.trim();
                console.log(personal.value);
                if(value == '')
                {
                    this.error = 'Tag cannot be empty';
                    return;
                }

                if(value.includes(' '))
                {
                    this.error = 'Tag cannot include empty space'
                    return;
                }

                if(value.length <= 2)
                {
                    this.error = 'Tag must contain more than 2 letter';
                    return;
                }
                Livewire.dispatch('tag' + this.eventName(), {data: {name: value, personal: personal.value}});
                this.closeModal();
            }
        }
    }
</script>
