<?php 
get_header();
$todo_id = get_the_id();
?>
    <section class="todo__header">
        <h1><?php echo the_title(); ?></h1>

    </section>
    <section class="todo__content">
        <div class="todo__list" data-list-target>

        </div>
        <div class="todo__create">
            <input type="text" class="todo__createName" autocomplete="off" data-todo-id="<?php echo $todo_id; ?>" placeholder="Type name here to create new todo item..." data-request-type="store" required />
        </div>
    </section>
    <script>
        const todo_id = <?php echo $todo_id; ?>;
    </script>
<?php
get_footer();
?>