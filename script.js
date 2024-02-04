const HTMLElements = {
    tableRoot: document.getElementById("table-root"),
    deleteButton: document.getElementById('delete'),
    nameInput: document.getElementById('name-input'),
    emailInput: document.getElementById('email-input'),
    phoneInput: document.getElementById('phone-input'),
    registerButton: document.getElementById('submit-button')
}

document.addEventListener('DOMContentLoaded',fetch_users())

function create_user(){

    if(HTMLElements.nameInput.value=== '' || HTMLElements.emailInput.value=== '' || HTMLElements.phoneInput.value=== ''){
        return alert("You must fill all the fields")
    }else{
            const userData = {
                name: HTMLElements.nameInput.value,
                email: HTMLElements.emailInput.value,
                phone_number: HTMLElements.phoneInput.value
            }

            fetch(`http://localhost:3000/server.php`,{
                method: "POST",
                body:JSON.stringify(userData)
            })
                .then((response)=>response.json())
                .then((data)=>{
                    console.log(data)
                    alert("Successfully created user !")
                    //reload to apply the changes
                    window.location.reload()
                })
                .catch((error)=>console.log("Error While Registering: " + error))
        }
}

function fetch_users(){
    fetch(`http://localhost:3000/server.php`,{
    method: 'GET',
    headers:{
        'Content-Type':'application/html'
    },
    mode: 'no-cors'
})
    .then((result) => result.text())
    .then((data)=>{

        HTMLElements.tableRoot.innerHTML = data
    })
    .catch((error)=>console.error(error))
}

function delete_user(id){

    fetch(`http://localhost:3000/server.php`,{
        method: "DELETE",
        body:JSON.stringify({id:id}),
        mode:'cors'
    })
    .then((response)=> response.json())
    .then((data)=> {
        console.log(data)
        alert("Successfully deleted registry !")
        //reload to apply the changes
        window.location.reload()
    })
    .catch((error)=> console.error(error))
}

function get_user_data(userToEditID){

    fetch(`http://localhost:3000/server.php`,{
        method: "PUT",
        body:JSON.stringify({id:userToEditID}),
        mode: 'cors'
    })
        .then((response)=> response.json())
        .then((userInfo)=> {
            HTMLElements.nameInput.value = userInfo.name
            HTMLElements.emailInput.value = userInfo.email
            HTMLElements.phoneInput.value = userInfo.phone_number
        })
        .catch((error)=> console.error("Error While Fetching Data: " + error))
}

function edit_user(id){

    HTMLElements.registerButton.innerText = "Update"

    /*  
        Automatically fills all fields
        with the selected user informations
    */
    get_user_data(id)

    //Monitorizes if the the 'submit' button was clicked again(update)

    HTMLElements.registerButton.addEventListener('click',(e)=>{

        e.preventDefault()

        /*
            Getts the new data typed
            in the form fields
        */

        const updatedData = {
            id:id,
            newName: HTMLElements.nameInput.value,
            newEmail: HTMLElements.emailInput.value,
            newPhone: HTMLElements.phoneInput.value,
            edit:true
        }

        fetch(`http://localhost:3000/server.php`,{
        method: "PUT",
        body: JSON.stringify(updatedData),
        })
            .then((response)=>{
                console.log(response)
                //reload to apply the changes
                alert("Successfully updated registry !")
                window.location.reload()
            })
            .catch((error)=>console.error("Error While updating: " + error))
        })
}