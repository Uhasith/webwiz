<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
</script>

<template>
    <AdminLayout>

        <div class="p-8">
            <div class="pl-3 pb-3">
                <h1 class="text-titlecolor font-semibold text-2xl">External Access</h1>
            </div>
            <div class="bg-[#F7FBF9] rounded-3xl p-6">
                <h2 class="text-black text-xl font-normal mb-8">External Access Form</h2>

                <div>
                    <form action="">
                        <div class="mb-3">
                            <label class="text-[#121619] font-medium text-base pl-1" for="nameinput">First name:</label><br>
                            <input class="border border-[#C1C7CD] rounded-3xl w-full font-normal text-sm bg-white" type="text" id="nameinput" name="nameinput" placeholder="Enter......">
                        </div>

                        <div class="mb-3">
                            <label class="text-[#121619] font-medium text-base pl-1" for="nameinput">Whitelisted apis:</label><br>
                            <textarea class="border border-[#C1C7CD] rounded-3xl w-full font-normal text-sm bg-white" type="text" id="nameinput" name="nameinput" placeholder="Enter......" rows="6" cols="50"></textarea>
                        </div>

                        <div class="flex gap-5 mb-3">
                            <div class="w-1/2">
                                <label class="text-[#121619] font-medium text-base pl-1" for="nameinput">Equipment</label><br>
                                <select class="border border-[#C1C7CD] rounded-3xl w-full font-normal text-sm bg-white" type="text" id="nameinput" name="nameinput" placeholder="Enter......">
                                    <option>Select</option>
                                </select>
                            </div>
                            <div class="w-1/2">
                                <label class="text-[#121619] font-medium text-base" for="nameinput">Location</label><br>
                                <select class="border border-[#C1C7CD] rounded-3xl w-full font-normal text-sm bg-white" type="text" id="nameinput" name="nameinput" placeholder="Enter......">
                                    <option>Select</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex gap-5 mb-3">
                            <div class="flex items-center gap-2 flex-row-reverse justify-end">
                                <label class="text-[#121619] font-medium text-base" for="nameinput">Recent Data</label><br>
                                <input class="border border-[#C1C7CD] rounded font-normal text-sm bg-white" type="checkbox" id="nameinput" name="nameinput" placeholder="Enter......">
                            </div>
                            <div class="flex items-center gap-2 flex-row-reverse justify-end">
                                <label class="text-[#121619] font-medium text-base" for="nameinput">Hourly data</label><br>
                                <input class="border border-[#C1C7CD] rounded font-normal text-sm bg-white" type="checkbox" id="nameinput" name="nameinput" placeholder="Enter......">
                            </div>
                            <div class="flex items-center gap-2 flex-row-reverse justify-end">
                                <label class="text-[#121619] font-medium text-base" for="nameinput">Daily data</label><br>
                                <input class="border border-[#C1C7CD] rounded font-normal text-sm bg-white" type="checkbox" id="nameinput" name="nameinput" placeholder="Enter......">
                            </div>
                            <div class="flex items-center gap-2 flex-row-reverse justify-end">
                                <label class="text-[#121619] font-medium text-base" for="nameinput">Monthly data</label><br>
                                <input class="border border-[#C1C7CD] rounded font-normal text-sm bg-white" type="checkbox" id="nameinput" name="nameinput" placeholder="Enter......">
                            </div>
                            <div class="flex items-center gap-2 flex-row-reverse justify-end">
                                <label class="text-[#121619] font-medium text-base" for="nameinput">Annualy data</label><br>
                                <input class="border border-[#C1C7CD] rounded font-normal text-sm bg-white" type="checkbox" id="nameinput" name="nameinput" placeholder="Enter......">
                            </div>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button class="border border-green1 rounded-3xl bg-white text-[#016553] px-8 py-2">Clear</button>
                            <button class="border border-[#01565B40] rounded-3xl bg-green1 text-white px-8 py-2">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AdminLayout>
</template>
