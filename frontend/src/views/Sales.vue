<script setup>
    import BaseInput from '@/components/BaseInput.vue';
    import BaseCard from '@/components/BaseCard.vue';
import ProductCard from '@/components/ProductCard.vue';
</script>

<template>
    <div class="grid grid-cols-3 bg-blue-100/30">
        <div class="col-span-2 flex flex-col p-4">
            <BaseInput
                height="h-[33px]"
                placeholder="Search"
                width="350px"
                icon="pi pi-search"
            />
            <div class="grid grid-cols-5 gap-3 mt-4 max-h-[550px] overflow-y-auto">
                <ProductCard 
                    name="Essence long lasting skin care product with extra moisture and glow essence" 
                    price="300,000" 
                    imageUrl="/src/assets/images/LUX.jpg"
                />
                <ProductCard 
                    name="Essence long lasting skin care product with extra moisture and glow essence" 
                    price="300,000" 
                    imageUrl="/src/assets/images/Burgilan.jpg"
                />
                <ProductCard 
                    name="Essence long lasting skin care product with extra moisture and glow essence" 
                    price="300,000" 
                    imageUrl="/src/assets/images/PAO.jpg"
                />
                <ProductCard 
                    name="Essence long lasting skin care product with extra moisture and glow essence" 
                    price="300,000" 
                    imageUrl="/src/assets/images/Darlie.jpg"
                />
                <ProductCard 
                    name="Essence long lasting skin care product with extra moisture and glow essence" 
                    price="300,000" 
                    imageUrl="/src/assets/images/PAO.jpg"
                />
                <ProductCard 
                    name="Essence long lasting skin care product with extra moisture and glow essence" 
                    price="300,000" 
                    imageUrl="/src/assets/images/Burgilan.jpg"
                />
                <ProductCard 
                    name="Essence long lasting skin care product with extra moisture and glow essence" 
                    price="300,000" 
                    imageUrl="/src/assets/images/Darlie.jpg"
                />
                <ProductCard 
                    name="Essence long lasting skin care product with extra moisture and glow essence" 
                    price="300,000" 
                    imageUrl="/src/assets/images/Darlie.jpg"
                />
                <ProductCard 
                    name="Essence long lasting skin care product with extra moisture and glow essence" 
                    price="300,000" 
                    imageUrl="/src/assets/images/PAO.jpg"
                />
                <ProductCard 
                    name="Essence long lasting skin care product with extra moisture and glow essence" 
                    price="300,000" 
                    imageUrl="/src/assets/images/Burgilan.jpg"
                />
                <ProductCard 
                    name="Essence long lasting skin care product with extra moisture and glow essence" 
                    price="300,000" 
                    imageUrl="/src/assets/images/LUX.jpg"
                />
                <ProductCard 
                    name="Essence long lasting skin care product with extra moisture and glow essence" 
                    price="300,000" 
                    imageUrl="/src/assets/images/Burgilan.jpg"
                />
                <ProductCard 
                    name="Essence long lasting skin care product with extra moisture and glow essence" 
                    price="300,000" 
                    imageUrl="/src/assets/images/PAO.jpg"
                />
                <ProductCard 
                    name="Essence long lasting skin care product with extra moisture and glow essence" 
                    price="300,000" 
                    imageUrl="/src/assets/images/Darlie.jpg"
                />
                <ProductCard 
                    name="Essence long lasting skin care product with extra moisture and glow essence" 
                    price="300,000" 
                    imageUrl="/src/assets/images/PAO.jpg"
                />
            </div>
        </div>
        <div class="bg-white text-black p-4 h-fit ">
            Hello
        </div>
    </div>
</template>